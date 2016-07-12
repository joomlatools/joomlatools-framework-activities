<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Denormalizeable Controller Behavior
 *
 * Allows for de-normalization of activities entries
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComActivitiesControllerBehaviorDenormalizeable extends KControllerBehaviorAbstract
{
    /**
     * The resources controller
     *
     * @var KModelInterface
     */
    protected $_controller;

    /**
     * A list of actions for cleaning up resources
     *
     * @var array
     */
    protected $_actions;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $config->controller;
        $this->_actions    = KObjectConfig::unbox($config->actions);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller' => 'resource',
            'actions'    => array('delete')
        ));

        parent::_initialize($config);
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if (!in_array($entity->action, $this->_actions))
        {
            $context = $this->getMixer()->getContext();

            $context->entity = $entity;

            $this->denormalize($context);
        }
        else $this->_getEntities($entity)->delete();
    }

    protected function _beforeDenormalize(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->entity instanceof KModelEntityInterface) {
            $context->entity = $this->getMixer()->fetch();
        }

        if ($context->entity instanceof ComActivitiesActivityInterface && $context->entity->getActivityObject())
        {
            $entity = $this->_getEntities($context->entity, array('last' => 1));

            if (!$entity->isNew()) {
                $result = $entity->title != $context->entity->title; // De-normalize only if title changed.
            }
        }
        else $result = false;

        return $result;
    }

    /**
     * De-normalizes an entity.
     *
     * @param KControllerContextInterface $context
     * @return bool
     */
    protected function _actionDenormalize(KControllerContextInterface $context)
    {
        $this->_getController()->add($this->_getData($context->entity));

        return true;
    }

    /**
     * De-normalized entities getter.
     *
     * @param KModelEntityInterface $entity     The normalized entity
     * @param array                 $conditions An optional array containing query conditions
     *
     * @return KModelEntityInterface|null The de-normalized entities
     */
    protected function _getEntities($entity, $conditions = array())
    {
        $model = $this->_getController()->getModel();

        $values = array_merge(array(
            'package'     => $entity->package,
            'name'        => $entity->name,
            'resource_id' => $entity->row
        ), $conditions);

        $model->reset()->getState()->setValues($values);

        return $model->fetch();
    }

    /**
     * De-normalized Data Getter
     *
     * @param KModelEntityInterface $entity The entity to de-normalize
     *
     * @return array The de-normalized data
     */
    protected function _getData(KModelEntityInterface $entity)
    {
        $uuid = $entity->row_uuid;

        if (!$uuid)
        {
            // Check if the resource already exists and grab its UUID.
            $entities = $this->_getEntities($entity, array('limit' => 1));

            if (!$entities->isNew()) {
                $uuid = $entities->uuid;
            } else {
                $uuid = $this->_uuid();
            }
        }

        return array(
            'package'     => $entity->package,
            'name'        => $entity->name,
            'resource_id' => $entity->row,
            'title'       => $entity->title,
            'uuid'        => $uuid
        );
    }

    /**
     * Controller setter.
     *
     * @param KControllerInterface $controller
     * @return ComActivitiesControllerBehaviorResourceable
     */
    protected function _setController(KControllerInterface $controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * Controller getter.
     *
     * @return KControllerInterface The controller
     */
    protected function _getController()
    {
        if (!$this->_controller instanceof  KControllerInterface)
        {
            if (strpos($this->_controller, '.') === false)
            {
                $parts         = $this->getMixer()->getIdentifier()->toArray();
                $parts['name'] = $this->_controller;

                $identifier = $this->getIdentifier($parts);
            }
            else $identifier = $this->getIdentifier($this->_model);

            $this->_setController($this->getObject($identifier));
        }

        return $this->_controller;
    }

    /**
     * Generates a Universally Unique Identifier, version 4.
     *
     * This function generates a truly random UUID.
     *
     * @param boolean   $hex If TRUE return the uuid in hex format, otherwise as a string
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @return string A UUID, made up of 36 characters or 16 hex digits.
     */
    protected function _uuid($hex = false)
    {
        $pr_bits = false;

        $fp = @fopen ( '/dev/urandom', 'rb' );
        if ($fp !== false)
        {
            $pr_bits = @fread ( $fp, 16 );
            @fclose ( $fp );
        }

        // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
        if(empty($pr_bits))
        {
            $pr_bits = "";
            for($cnt = 0; $cnt < 16; $cnt ++) {
                $pr_bits .= chr ( mt_rand ( 0, 255 ) );
            }
        }

        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec ( $time_hi_and_version );
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        //Either return as hex or as string
        $format = $hex ? '%08s%04s%04x%04x%012s' : '%08s-%04s-%04x-%04x-%012s';

        return sprintf ( $format, $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );
    }
}
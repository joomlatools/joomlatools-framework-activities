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

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $config->controller;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller' => 'resource'
        ));

        if ($this->getIdentifier()->getPackage() != 'activities')
        {
            $aliases = array(
                'com:activities.controller.resource' => array(
                    'path' => array('controller'),
                    'name' => 'resource'
                )
            );

            foreach ($aliases as $identifier => $alias)
            {
                $alias = array_merge($this->getMixer()->getIdentifier()->toArray(), $alias);

                $manager = $this->getObject('manager');

                // Register the alias if a class for it cannot be found.
                if (!$manager->getClass($alias, false)) {
                    $manager->registerAlias($identifier, $alias);
                }
            }
        }

        parent::_initialize($config);
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if ($entity->action != 'delete')
        {
            $context = $this->getMixer()->getContext();

            $context->entity = $entity;

            $this->denormalize($context);
        }
        else $this->_getEntities($this->_getData($entity))->delete();
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
            'package' => $entity->package,
            'name'    => $entity->name,
            'row'     => $entity->row
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
        return array(
            'package' => $entity->package,
            'name'    => $entity->name,
            'row'     => $entity->row,
            'title'   => $entity->title
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
}
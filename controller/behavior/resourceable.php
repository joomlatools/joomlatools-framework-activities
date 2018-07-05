<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Resourceable Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComActivitiesControllerBehaviorResourceable extends KControllerBehaviorAbstract
{
    /**
     * A list of actions for cleaning up resources
     *
     * @var array
     */
    protected $_actions;

    /**
     * Resource controller
     *
     * @var KControllerInterface
     */
    protected $_controller = 'com:activities.controller.resource';

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_actions = KObjectConfig::unbox($config->actions);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'actions' => array('delete')
        ));

        parent::_initialize($config);
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if ($entity instanceof ComActivitiesActivityInterface && $entity->getActivityObject())
        {
            $resource = $entity->getResource();

            if (!in_array($entity->action, $this->_actions))
            {
                if ($resource->isNew() || $resource->isModified()) {
                    $resource->save();
                }
            }
            else if (!$resource->isNew()) $resource->delete();
        }
    }

    /**
     * Resource controller getter.
     *
     * @return KControllerInterface The controller
     */
    protected function _getController()
    {
        if (!$this->_controller instanceof KControllerInterface) {
            $this->_controller = $this->getObject($this->_controller);
        }

        return $this->_controller;
    }
}
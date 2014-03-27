<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Loggable Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerBehaviorLoggable extends KControllerBehaviorAbstract
{
    /**
     * List of actions to log
     *
     * @var array
     */
    protected $_actions;

    /**
     * The name of the column to use as the title column in the log entry
     *
     * @var string
     */
    protected $_title_column;

    /**
     * Activity controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_actions      = KObjectConfig::unbox($config->actions);
        $this->_title_column = KObjectConfig::unbox($config->title_column);
        $this->_controller   = $config->controller;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'     => self::PRIORITY_LOWEST,
            'actions'      => array('after.edit', 'after.add', 'after.delete'),
            'title_column' => array('title', 'name'),
            'controller'   => 'com:activities.controller.activity'
        ));

        parent::_initialize($config);
    }

    public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $name = $command->getName();

        if (in_array($name, $this->_actions))
        {
            $parts = explode('.', $name);

            // Properly fetch data for the event.
            if ($parts[0] == 'before') {
                $data = $this->getMixer()->getModel()->fetch();
            } else {
                $data = $command->result;
            }

            if ($data instanceof KModelEntityInterface)
            {
                foreach ($data as $entity)
                {
                    //Only log if the row status is valid.
                    $status = $this->_getStatus($entity, $name);

                    if (!empty($status) && $status !== KDatabase::STATUS_FAILED)
                    {
                        $config = new KObjectConfig(array(
                            'entity'  => $entity,
                            'status'  => $status,
                            'command' => $command));

                        try {
                            $this->getObject($this->_controller)->add($this->_getActivityData($config));
                        }
                        catch (Exception $e)
                        {
                            if (JDEBUG) {
                                throw $e;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Activity data getter.
     *
     * @param KObjectConfig $config Configuration object containing event related information.
     *
     * @return array Activity data.
     */
    protected function _getActivityData(KObjectConfig $config)
    {
        $command    = $config->command;
        $identifier = $this->getActivityIdentifier($command);

        $data = array(
            'action'      => $command->action,
            'application' => $identifier->domain,
            'type'        => $identifier->type,
            'package'     => $identifier->package,
            'name'        => $identifier->name,
            'status'      => $config->status
        );

        $entity = $config->entity;

        if (is_array($this->_title_column))
        {
            foreach ($this->_title_column as $title)
            {
                if ($entity->{$title})
                {
                    $data['title'] = $entity->{$title};
                    break;
                }
            }
        }
        elseif ($entity->{$this->_title_column}) {
            $data['title'] = $entity->{$this->_title_column};
        }

        if (!isset($data['title'])) {
            $data['title'] = '#' . $entity->id;
        }

        $data['row'] = $entity->id;

        return $data;
    }

    /**
     * Status getter.
     *
     * @param KModelEntityInterface $entity
     * @param string                $action The command action being executed.
     * @return string
     */
    protected function _getStatus(KModelEntityInterface $entity, $action)
    {
        $status = $entity->getStatus();

        // Commands may change the original status of an action.
        if ($action == 'after.add' && $status == KDatabase::STATUS_UPDATED) {
            $status = KDatabase::STATUS_CREATED;
        }

        return $status;
    }

    /**
     * Activity identifier getter.
     *
     * @param KCommandInterface $context The command context object.
     *
     * @return KObjectIdentifier The activity identifier.
     */
    public function getActivityIdentifier(KCommandInterface $command)
    {
        return $command->getSubject()->getIdentifier();
    }

    public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}

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
class ComActivitiesControllerBehaviorLoggable extends KControllerBehaviorAbstract implements ComActivitiesActivityLoggerInterface
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

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_title_column = KObjectConfig::unbox($config->title_column);
        $this->_controller   = $config->controller;

        $this->setActions(KObjectConfig::unbox($config->actions));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
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

    /**
     * Log an activity
     *
     * @param string $action                       The action to log
     * @param KModelEntityInterface $object        The activity object on which the action is performed
     * @param KObjectIdentifierInterface $subject  The activity subject who is performing the action
     * @return ComActivitiesActivityLoggerInterface
     */
    public function log($action, KModelEntityInterface $object, KObjectIdentifierInterface $subject)
    {
        $controller = $this->getObject($this->_controller);

        if($controller instanceof KControllerModellable)
        {
            foreach($object as $entity)
            {
                //Only log if the entity status is valid.
                $status = $this->getActivityStatus($entity, $action);

                if (!empty($status) && $status !== $entity::STATUS_FAILED)
                {
                    //Get the activity data
                    $data = $this->getActivityData($entity, $subject);

                    //Set the status
                    if(!isset($data['status'] )) {
                        $data['status'] = $status;
                    }

                    //Set the action
                    if(!isset($data['action']))
                    {
                        $parts = explode('.', $action);
                        $data['action'] = $parts[1];
                    }

                    $controller->add($data);
                }
            }
        }

        return $this;
    }

    /**
     * Command handler
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    final public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $action = $command->getName();

        if (in_array($action, $this->getActions()))
        {
            $object = $this->getActivityObject($command);

            if ($object instanceof KModelEntityInterface)
            {
                $subject = $this->getActivitySubject($command);
                $this->log($action, $object, $subject);
            }
        }
    }

    /**
     * Get the behavior name
     *
     * Hardcode the name to 'loggable'.
     *
     * @return string
     */
    public function getName()
    {
        return 'loggable';
    }

    /**
     * Return a list of actions the logger should log
     *
     * @return array List of actions
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * Return a list of actions the logger should log
     *
     * @param array $actions List of actions
     * @return ComActivitiesActivityLoggerInterface
     */
    public function setActions($actions)
    {
        $this->_actions = $actions;
        return $this;
    }

    /**
     * Get the activity object
     *
     * The activity object is the entity on which the action is executed.
     *
     * @param KCommandInterface $command The command.
     * @return KModelEntityInterface The entity.
     */
    public function getActivityObject(KCommandInterface $command)
    {
        $parts = explode('.', $command->getName());

        // Properly fetch data for the event.
        if ($parts[0] == 'before') {
            $object = $this->getMixer()->getModel()->fetch();
        } else {
            $object = $command->result;
        }

        return $object;
    }

    /**
     * Get the activity status
     *
     * The activity state is the status of the entity at the time the action happened
     *
     * @param KModelEntityInterface $object The activity object on which the action is performed
     * @param string                $action The command action being executed.
     * @return string
     */
    public function getActivityStatus(KModelEntityInterface $object, $action = null)
    {
        $status = $object->getStatus();

        // Commands may change the original status of an action.
        if ($action == 'after.add' && $status == $object::STATUS_UPDATED) {
            $status = $object::STATUS_CREATED;
        }

        return $status;
    }

    /**
     * Get the activity subject
     *
     * The activity subject is the identifier of the entity that generates the event.
     *
     * @param KCommandInterface $context The command context object.
     * @return KObjectIdentifier The activity identifier.
     */
    public function getActivitySubject(KCommandInterface $command)
    {
        return $command->getSubject()->getIdentifier();
    }

    /**
     * Get the activity data
     *
     * @param KModelEntityInterface $object        The activity object on which the action is performed
     * @param KObjectIdentifierInterface $subject  The activity subject who is performing the action
     * @return array Activity data.
     */
    public function getActivityData(KModelEntityInterface $object, KObjectIdentifierInterface $subject)
    {
        $data = array(
            'application' => $subject->domain,
            'type'        => $subject->type,
            'package'     => $subject->package,
            'name'        => $subject->name,
        );

        if (is_array($this->_title_column))
        {
            foreach ($this->_title_column as $title)
            {
                if ($object->{$title})
                {
                    $data['title'] = $object->{$title};
                    break;
                }
            }
        }
        elseif ($object->{$this->_title_column}) {
            $data['title'] = $object->{$this->_title_column};
        }

        if (!isset($data['title'])) {
            $data['title'] = '#' . $object->id;
        }

        $data['row'] = $object->id;

        return $data;
    }

    /**
     * Get an object handle
     *
     * Force the object to be enqueue in the command chain.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}

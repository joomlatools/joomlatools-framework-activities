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
            'controller'   => 'com://admin/activities.controller.activity'
        ));

        parent::_initialize($config);
    }

    public function execute($name, KCommand $context)
    {
        if (in_array($name, $this->_actions)) {

            $parts = explode('.', $name);

            // Properly fetch data for the event.
            if ($parts[0] == 'before') {
                $data = $this->getMixer()->getModel()->getData();
            } else {
                $data = $context->result;
            }

            if ($data instanceof KDatabaseRowInterface || $data instanceof KDatabaseRowsetInterface) {
                $rowset = array();

                if ($data instanceof KDatabaseRowInterface) {
                    $rowset[] = $data;
                } else {
                    $rowset = $data;
                }

                foreach ($rowset as $row) {
                    //Only log if the row status is valid.
                    $status = $this->_getStatus($row, $name);

                    if (!empty($status) && $status !== KDatabase::STATUS_FAILED)
                    {
                        $config = new KObjectConfig(array(
                            'row'     => $row,
                            'status'  => $status,
                            'context' => $context,
                            'event'   => $name));

                        $this->getObject($this->_controller)->add($this->_getActivityData($config));
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
        $context = $config->context;

        $identifier = $this->getActivityIdentifier($context);

        $data = array(
            'action'      => $context->action,
            'application' => $identifier->application,
            'type'        => $identifier->type,
            'package'     => $identifier->package,
            'name'        => $identifier->name,
            'status'      => $config->status
        );

        $row = $config->row;

        if (is_array($this->_title_column))
        {
            foreach ($this->_title_column as $title)
            {
                if ($row->{$title})
                {
                    $data['title'] = $row->{$title};
                    break;
                }
            }
        }
        elseif ($row->{$this->_title_column})
        {
            $data['title'] = $row->{$this->_title_column};
        }

        if (!isset($data['title']))
        {
            $data['title'] = '#' . $row->id;
        }

        $data['row'] = $row->id;

        return $data;
    }

    /**
     * Status getter.
     *
     * @param KDatabaseRowInterface $row
     * @param string               $action    The command action being executed.
     */
    protected function _getStatus(KDatabaseRowInterface $row, $action)
    {
        $status = $row->getStatus();

        // Commands may change the original status of an action.
        if ($action == 'after.add' && $status == KDatabase::STATUS_UPDATED) {
            $status = KDatabase::STATUS_CREATED;
        }

        return $status;
    }

    /**
     * Activity identifier getter.
     *
     * @param KCommand $context The command context object.
     *
     * @return KObjectIdentifier The activity identifier.
     */
    public function getActivityIdentifier(KCommand $context)
    {
        return $context->caller->getIdentifier();
    }

    public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}

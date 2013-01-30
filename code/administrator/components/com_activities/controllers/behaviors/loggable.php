<?php
/**
 * @version		$Id: loggable.php 1485 2012-02-10 12:32:02Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Activities
 * @copyright	Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Loggable Controller Behavior Class
 *
 * @author      Israel Canasa <http://nooku.assembla.com/profile/israelcanasa>
 * @package    	Nooku_Components
 * @subpackage 	Activities
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
     * @param KConfig
     */
    protected $_activity_controller;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_actions      = KConfig::unbox($config->actions);
        $this->_title_column = KConfig::unbox($config->title_column);
        $this->_activity_controller = $config->activity_controller;
    }

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'     => KCommand::PRIORITY_LOWEST,
            'actions'      => array('after.edit', 'after.add', 'after.delete'),
            'title_column' => array('title', 'name'),
            'activity_controller' => array(
                'identifier' => 'com://admin/activities.controller.activity',
                'config'     => array()),
        ));

        parent::_initialize($config);
    }

    public function execute($name, KCommandContext $context)
    {
        if (in_array($name, $this->_actions)) {

            $data = $context->result;

            if ($data instanceof KDatabaseRowAbstract || $data instanceof KDatabaseRowsetAbstract) {
                $rowset = array();

                if ($data instanceof KDatabaseRowAbstract) {
                    $rowset[] = $data;
                } else {
                    $rowset = $data;
                }

                foreach ($rowset as $row) {
                    //Only log if the row status is valid.
                    $status = $this->_getStatus($row, $name);

                    if (!empty($status) && $status !== KDatabase::STATUS_FAILED) {
                        $this->getService($this->_activity_controller->identifier,
                            KConfig::unbox($this->_activity_controller->config))->add($this->_getActivityData($row,
                            $status, $context));
                    }
                }
            }
        }
    }

    /**
     * Returns activity data given a row and its context.
     *
     * This method can be used if the default data mapping does not apply.
     *
     * @param KDatabaseRowAbstract $row     The data row.
     * @param                      string   The row status.
     * @param KCommandContext      $context The command context.
     *
     * @return array Activity data.
     */
    protected function _getActivityData(KDatabaseRowAbstract $row, $status, KCommandContext $context)
    {

        $identifier = $this->getActivityIdentifier($context);

        $activity = array(
            'action'      => $context->action,
            'application' => $identifier->application,
            'type'        => $identifier->type,
            'package'     => $identifier->package,
            'name'        => $identifier->name,
            'status'      => $status
        );

        if (is_array($this->_title_column)) {
            foreach ($this->_title_column as $title) {
                if ($row->{$title}) {
                    $activity['title'] = $row->{$title};
                    break;
                }
            }
        } elseif ($row->{$this->_title_column}) {
            $activity['title'] = $row->{$this->_title_column};
        }

        if (!isset($activity['title'])) {
            $activity['title'] = '#' . $row->id;
        }

        $activity['row'] = $row->id;

        return $activity;
    }

    /**
     * Status getter.
     *
     * Loggable support actions other than add, edit and delete. While logging custom actions it may be
     * useful to somehow translate the returned status to something more meaningful.
     *
     * @param KDatabaseRowAbstract       $row
     * @param string                     $action    The command action being executed.
     */
    protected function _getStatus(KDatabaseRowAbstract $row, $action)
    {
        $status = $row->getStatus();

        // Commands may change the original status of an action.
        if ($action == 'after.add' && $status == KDatabase::STATUS_UPDATED) {
            $status = KDatabase::STATUS_CREATED;
        }

        return $status;
    }

    /**
     * This method is called with the current context to determine what identifier generates the event.
     *
     * This is useful in cases where the row is from another package or the actual action happens somewhere else.
     *
     * @param KCommandContext $context
     */
    public function getActivityIdentifier(KCommandContext $context)
    {
        return $context->caller->getIdentifier();
    }

    public function getHandle()
    {
        return KMixinAbstract::getHandle();
    }
}
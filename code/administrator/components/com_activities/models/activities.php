<?php
/**
 * @version		$Id: activities.php 1485 2012-02-10 12:32:02Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Activities
 * @copyright	Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Activities Model Class
 *
 * @author      Israel Canasa <http://nooku.assembla.com/profile/israelcanasa>
 * @package    	Nooku_Components
 * @subpackage 	Activities
 */

class ComActivitiesModelActivities extends ComKoowaModelDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_state
			->insert('application' , 'cmd')
			->insert('type'        , 'cmd')
			->insert('package'     , 'cmd')
			->insert('name'        , 'cmd')
			->insert('action'      , 'cmd')
            ->insert('row'         , 'int')
			->insert('user'        , 'cmd')
			->insert('distinct'    , 'boolean', false)
			->insert('column'      , 'cmd')
			->insert('start_date'  , 'date')
			->insert('end_date'    , 'date')
			->insert('day_range'   , 'int')
            ->insert('ip'          , 'ip');

		$this->_state->remove('direction')->insert('direction', 'word', 'desc');

		// Force ordering by created_on
		$this->_state->sort = 'created_on';
	}
	
	/**
	 * Used by the purge action to add necessary where clauses
	 * 
	 * @param KDatabaseQueryInterface $query
	 */
	public function buildDeleteQuery(KDatabaseQueryDelete $query)
	{
		$this->_buildQueryWhere($query);
	}

	protected function _buildQueryColumns(KDatabaseQueryInterface $query)
	{
		if($this->_state->distinct && !empty($this->_state->column))
		{
			$query->distinct()
				->columns($this->_state->column)
				->columns(array('activities_activity_id' => $this->_state->column));
		}
		else
		{
			parent::_buildQueryColumns($query);
			$query->columns(array('created_by_name' => 'users.name'));
		}
	}

	protected function _buildQueryJoins(KDatabaseQueryInterface $query)
	{
		$query->join(array('users' => 'users'), 'users.id = tbl.created_by');
	}

	protected function _buildQueryWhere(KDatabaseQueryInterface $query)
	{
		parent::_buildQueryWhere($query);
		
		$state = $this->_state;

		if ($state->application) {
			$query->where('tbl.application = :application')->bind(array('application' => $state->application));
		}

		if ($state->type) {
			$query->where('tbl.type = :type')->bind(array('type' => $state->type));
		}

		if ($state->package && !($state->distinct && !empty($state->column))) {
			$query->where('tbl.package = :package')->bind(array('package' => $state->package));
		}

		if ($state->name) {
			$query->where('tbl.name = :name')->bind(array('name' => $state->name));
		}

		if ($state->action) {
			$query->where('tbl.action IN (:action)')->bind(array('action' => $state->action));
		}

        if (is_numeric($state->row)) {
        	$query->where('tbl.row IN (:row)')->bind(array('row' => $state->row));
        }

        if ($state->start_date && $state->start_date != '0000-00-00')
		{
			$start_date = $this->getService('koowa:date', array('date' => $state->start_date));
			$start      = $start_date->getDate();

			$query->where('tbl.created_on >= :created_on')->bind(array('created_on' => $start));
			
			if ($day_range = $state->day_range) {
			    $range = clone $start_date;  
			    $query->where('tbl.created_on < :created_on')->bind(array('created_on' => $range->addDays($day_range)->getDate()));
			}
		}
		
		if ($state->end_date && $state->end_date != '0000-00-00')
		{
		    $end_date  = $this->getService('koowa:date', array('date' => $state->end_date));
		    $end       = $end_date->getDate();

		    $query->where('tbl.created_on <= :created_on')->bind(array('created_on' => $end));
		    
		    if ($day_range = $state->day_range) {
		        $range = clone $end_date;
		        $query->where('tbl.created_on > :created_on')->bind(array('created_on' => $range->addDays(-$day_range)->getDate()));
		    }
		}

		if ($state->user) {
			$query->where('tbl.created_by = :created_by')->bind(array('created_by' => $state->user));
		}

        if ($ip = $state->ip) {
        	$query->where('tbl.ip IN (:ip)')->bind(array('ip' => $state->ip));
        }
	}

	protected function _buildQueryOrder(KDatabaseQueryInterface $query)
	{
		if($this->_state->distinct && !empty($this->_state->column)) {
			$query->order('package', 'asc');
		} else {
		    parent::_buildQueryOrder($query);
		}
	}
}
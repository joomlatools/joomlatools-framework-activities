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

class ComActivitiesModelActivities extends ComDefaultModelDefault
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
	
	public function getListQuery()
	{
	    $query  = null;
	    
	    if(!$this->_state->isEmpty())
	    {
	        $query = $this->getTable()->getDatabase()->getQuery();
	    
	        $this->_buildQueryColumns($query);
	        $this->_buildQueryFrom($query);
	        $this->_buildQueryJoins($query);
	        $this->_buildQueryWhere($query);
	        $this->_buildQueryGroup($query);
	        $this->_buildQueryHaving($query);
	        $this->_buildQueryOrder($query);
	        $this->_buildQueryLimit($query);
	    }
	    
	    return $query;
	}

	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		if($this->_state->distinct && !empty($this->_state->column))
		{
			$query->distinct()
				->select($this->_state->column)
				->select($this->_state->column . ' AS activities_activity_id');
		}
		else
		{
			parent::_buildQueryColumns($query);
			$query->select('users.name AS created_by_name');
		}
	}

	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		$query->join('LEFT', 'users AS users', 'users.id = tbl.created_by');
	}

	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if ($this->_state->application) {
			$query->where('tbl.application', '=', $this->_state->application);
		}

		if ($this->_state->type) {
			$query->where('tbl.type', '=', $this->_state->type);
		}

		if ($this->_state->package && !($this->_state->distinct && !empty($this->_state->column))) {
			$query->where('tbl.package', '=', $this->_state->package);
		}

		if ($this->_state->name) {
			$query->where('tbl.name', '=', $this->_state->name);
		}

		if ($this->_state->action) {
			$query->where('tbl.action', 'IN', $this->_state->action);
		}

        if (is_numeric($this->_state->row)) {
            $query->where('tbl.row', 'IN', $this->_state->row);
        }

        if ($this->_state->start_date && $this->_state->start_date != '0000-00-00')
		{
			$start_date = $this->getService('koowa:date', array('date' => $this->_state->start_date));
			$start      = $start_date->getDate();

			$query->where('tbl.created_on', '>=', $start);
			
			if ($day_range = $this->_state->day_range) {
			    $range = clone $start_date;  
			    $query->where('tbl.created_on', '<', $range->addDays($day_range)->getDate());
			}
		}
		
		if ($this->_state->end_date && $this->_state->end_date != '0000-00-00')
		{
		    $end_date  = $this->getService('koowa:date', array('date' => $this->_state->end_date));
		    $end       = $end_date->getDate();

		    $query->where('tbl.created_on', '<=', $end);
		    
		    if ($day_range = $this->_state->day_range) {
		        $range = clone $end_date;
		        $query->where('tbl.created_on', '>', $range->addDays(-$day_range)->getDate());
		    }
		}

		if ($this->_state->user) {
			$query->where('tbl.created_by', '=', $this->_state->user);
		}

        if ($ip = $this->_state->ip) {
            $query->where('tbl.ip', 'IN', $ip);
        }
	}

	protected function _buildQueryOrder(KDatabaseQuery $query)
	{
		if($this->_state->distinct && !empty($this->_state->column)) {
			$query->order('package', 'asc');
		} else {
		    parent::_buildQueryOrder($query);
		}
	}
}
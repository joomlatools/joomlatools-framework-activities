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

    public function getPurgeQuery()
    {
        $state = $this->getState();

        $query = $this->getTable()->getDatabase()->getQuery();

        $query->from($this->getTable()->getName());

        if ($state->end_date && $state->end_date != '0000-00-00')
        {
            $end_date = $this->getService('koowa:date', array('date' => $state->end_date));
            $end      = $end_date->getDate('%Y-%m-%d');

            $query->where('DATE(created_on)', '<=', $end);
        }

        $query = 'DELETE ' . (string) $query;

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
			$query->select(array('users.name AS created_by_name'));
		}
	}

	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
        $query->join('LEFT', 'users AS users', 'users.id = tbl.created_by');
    }

	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
		
		$state = $this->_state;

		if ($state->application) {
			$query->where('tbl.application','=',$state->application);
		}

		if ($state->type) {
			$query->where('tbl.type','=',$state->type);
		}

		if ($state->package && !($state->distinct && !empty($state->column))) {
			$query->where('tbl.package','=',$state->package);
		}

		if ($state->name) {
			$query->where('tbl.name','=',$state->name);
		}

		if ($state->action) {
			$query->where('tbl.action','IN',$state->action);
		}

        if (is_numeric($state->row)) {
        	$query->where('tbl.row','IN',$state->row);
        }

        if ($state->start_date && $state->start_date != '0000-00-00')
		{
			$start_date = $this->getService('koowa:date', array('date' => $state->start_date));
			$start      = $start_date->getDate();

			$query->where('tbl.created_on','>=',$start);
			
			if ($day_range = $state->day_range) {
			    $range = clone $start_date;  
			    $query->where('tbl.created_on','<',$range->addDays($day_range)->getDate());
			}
		}
		
		if ($state->end_date && $state->end_date != '0000-00-00')
		{
		    $end_date  = $this->getService('koowa:date', array('date' => $state->end_date));
		    $end       = $end_date->getDate('%Y-%m-%d');

		    $query->where('DATE(tbl.created_on)','<=',$end);
		    
		    if ($day_range = $state->day_range) {
		        $range = clone $end_date;
		        $query->where('DATE(tbl.created_on)','>=',$range->addDays(-$day_range)->getDate('%Y-%m-%d'));
		    }
		}

		if ($state->user) {
			$query->where('tbl.created_by','=',$state->user);
		}

        if ($ip = $state->ip) {
        	$query->where('tbl.ip','IN',$state->ip);
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

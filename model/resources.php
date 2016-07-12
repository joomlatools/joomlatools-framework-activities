<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Resources Model
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesModelResources extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
             ->insert('uuid', 'string')
             ->insert('package', 'cmd')
             ->insert('name', 'cmd')
             ->insert('row', 'string')
             ->insert('title', 'string')
             ->insert('last', 'int');
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $state = $this->getState();

        $table = $this->getTable();

        $condition = sprintf('tbl.package = j.package AND tbl.name = j.name AND tbl.row = j.row AND tbl.%1$s < j.%1$s', $table->getIdentityColumn());

        if ($state->last) {
            $query->join(sprintf('%s AS j', $table->getBase()), $condition, 'LEFT');
        }
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($uuid = $state->uuid) {
            $query->where('tbl.uuid = :uuid')->bind(array('uuid' => $uuid));
        }

        if ($package = $state->package) {
            $query->where('tbl.package = :package')->bind(array('package' => $package));
        }

        if ($name = $state->name) {
            $query->where('tbl.name = :name')->bind(array('name' => $name));
        }

        if ($row = $state->row) {
            $query->where('tbl.row = :row')->bind(array('row' => $row));
        }

        if ($title = $state->title) {
            $query->where('tbl.title LIKE :title')->bind(array('title' => '%' . $title . '%'));
        }

        if ($state->last) {
            $query->where(sprintf('j.%s IS NULL', $this->getTable()->getIdentityColumn()));
        }
    }
}
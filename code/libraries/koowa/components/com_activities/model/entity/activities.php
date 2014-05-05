<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesModelEntityActivities extends KModelEntityRowset
{
    /**
     * Overridden to set prototype based on current activity data.
     */
    public function create(array $properties = array(), $status = null)
    {
        $this->_prototype = $this->getTable()->createRow(array('activity' => $properties));

        return parent::create($properties, $status);
    }
}
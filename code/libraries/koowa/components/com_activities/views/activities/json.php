<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Json View
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 * @see     http://activitystrea.ms/specs/json/1.0/
 */
class ComActivitiesViewActivitiesJson extends KViewJson
{
    protected function _getItem(KDatabaseRowInterface $row)
    {
        if ($this->getLayout() == 'stream')
        {
            $item = $row->getStreamData();
        }
        else
        {
            $item = parent::_getItem($row);
        }

        return $item;
    }

    protected function _getList(KDatabaseRowsetInterface $rowset)
    {
        $result = array();

        foreach ($rowset as $row)
        {
            $result[] = $this->_getItem($row);
        }

        return $result;
    }
}

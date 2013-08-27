<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Database Table
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesDatabaseTableActivities extends KDatabaseTableDefault
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('creatable', 'identifiable')
        ));

        parent::_initialize($config);
    }
}

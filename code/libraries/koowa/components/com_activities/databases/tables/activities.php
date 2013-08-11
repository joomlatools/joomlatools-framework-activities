<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Database Table Class
 *
 * @author         Israel Canasa <http://nooku.assembla.com/profile/israelcanasa>
 * @package        Nooku_Components
 * @subpackage     Activities
 */

class ComActivitiesDatabaseTableActivities extends KDatabaseTableDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('creatable', 'identifiable')
        ));

        parent::_initialize($config);
    }
}

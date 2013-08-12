<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Executable Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerBehaviorExecutable extends ComKoowaControllerBehaviorExecutable
{
    public function canAdd()
    {
        $result = false;
        if (!$this->_mixer->isDispacthed()) {
            $result = true;
        }
        return $result;
    }

    public function canEdit()
    {
        return false;
    }
}

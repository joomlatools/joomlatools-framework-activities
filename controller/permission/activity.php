<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Executable Controller Behavior.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerPermissionActivity extends KControllerPermissionAbstract
{
    public function canAdd()
    {
        return !$this->isDispatched(); // Do not allow activities to be added if the controller is not dispatched.
    }

    public function canEdit()
    {
        return false; // Do not allow activities to be edited.
    }

    public function canPurge()
    {
       return !$this->isDispatched() || $this->canDelete();
    }
}

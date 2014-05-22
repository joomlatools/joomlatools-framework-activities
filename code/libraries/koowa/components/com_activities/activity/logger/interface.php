<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Logger Interface.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityLoggerInterface
{
    /**
     * Log an activity
     *
     * @param string $action                       The action to log
     * @param KModelEntityInterface $object        The activity object on which the action is performed
     * @param KObjectIdentifierInterface $subject  The activity subject who is performing the action
     * @return mixed|null If the logger breaks, returns the break condition. NULL otherwise.
     */
    public function log($action, KModelEntityInterface $object, KObjectIdentifierInterface $subject);

    /**
     * Return a list of actions the logger should log
     *
     * @return array List of actions
     */
    public function getActions();

    /**
     * Return a list of actions the logger should log
     *
     * @param array $actions List of actions
     * @return ComActivitiesActivityLoggerInterface
     */
    public function setActions($actions);

    /**
     * Get the activity object
     *
     * The activity object is the entity on which the action is executed.
     *
     * @param KCommandInterface $command The command.
     * @return KModelEntityInterface The entity.
     */
    public function getActivityObject(KCommandInterface $command);

    /**
     * Get the activity identifier
     *
     * The activity subject is the identifier of the entity that generates the event.
     *
     * @param KCommandInterface $context The command context object.
     * @return KObjectIdentifier The activity identifier.
     */
    public function getActivitySubject(KCommandInterface $command);

    /**
     * Get the activity status
     *
     * The activity state is the status of the entity at the time the action happened
     *
     * @param KModelEntityInterface $object The activity object on which the action is performed
     * @param string                $action The command action being executed.
     * @return string
     */
    public function getActivityStatus(KModelEntityInterface $object, $action = null);

     /**
      * Get the activity data
      *
      * @param KModelEntityInterface $object        The activity object on which the action is performed
      * @param KObjectIdentifierInterface $subject  The activity subject who is performing the action
      * @return array Activity data.
      */
    public function getActivityData(KModelEntityInterface $object, KObjectIdentifierInterface $subject);
}
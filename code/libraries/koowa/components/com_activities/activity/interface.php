<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Interface.
 *
 * In its simplest form, an activity consists of an actor, a verb, an object, and optionally a target. It tells the
 * story of a person performing an action on or with an object -- "Geraldine posted a photo to her album" or "John
 * shared a video". In most cases these components will be explicit, but they may also be implied.
 *
 * @see http://activitystrea.ms/specs/json/1.0/
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityInterface
{
    /**
     * Get the activity message string format
     *
     * The message string format is a compact representation of the activity which also provides information about the
     * parameters it may contain.
     *
     * @return string The activity message string format.
     */
    public function getMessageFormat();

    /**
     * Activity message parameters getter.
     *
     * @return array An array of ComActivitiesActivityParameterInterface objects.
     */
    public function getMessageParameters();

    /**
     * Activity stream objects getter.
     *
     * @return Array An array of ComActivitiesActivityObjectInterface objects.
     */
    public function getStreamObjects();

    /**
     * Looks for the activity actor.
     *
     * @return boolean True if found, false otherwise.
     */
    public function findActor();

    /**
     * Looks for the activity object.
     *
     * @return boolean True if found, false otherwise.
     */
    public function findObject();

    /**
     * Looks for the activity target.
     *
     * @return bool|null True if found, false if not found, null if the activity has no target.
     */
    public function findTarget();

    /**
     * Casts an activity to a string.
     *
     * @return string The string representation of an activity
     */
    public function toString();
}
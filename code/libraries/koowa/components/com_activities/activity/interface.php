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
     * Get the activity string format
     *
     * The string format is a compact representation of the activity which also provides information about the
     * parameters it may contain.
     *
     * @return string The activity string format.
     */
    public function getFormat();

    /**
     * Get the activity parameters
     *
     * @return array The activity parameters.
     */
    public function getParameters();

    /**
     * Looks for the activity actor.
     *
     * @return boolean True if found, false otherwise.
     */
    public function findActor();

    /**
     * Activity actor id getter.
     *
     * @return mixed The activity actor id.
     */
    public function getActorId();

    /**
     * Activity actor URL getter.
     *
     * @return mixed The activity actor url.
     */
    public function getActorUrl();

    /**
     * Activity actor type getter.
     *
     * @return mixed The activity actor type.
     */
    public function getActorType();

    /**
     * Looks for the activity object.
     *
     * @return boolean True if found, false otherwise.
     */
    public function findObject();

    /**
     * Activity object id getter.
     *
     * @return mixed The activity object id.
     */
    public function getObjectId();

    /**
     * Activity object URL getter.
     *
     * @return mixed The activity object url.
     */
    public function getObjectUrl();

    /**
     * Activity object type getter.
     *
     * @return mixed The activity object type.
     */
    public function getObjectType();

    /**
     * Looks for the activity target.
     *
     * @return bool|null True if found, false if not found, null if the activity has no target.
     */
    public function findTarget();

    /**
     * Activity target id getter.
     *
     * @return mixed The activity target id.
     */
    public function getTargetId();

    /**
     * Activity target URL getter.
     *
     * @return mixed The activity target URL.
     */
    public function getTargetUrl();

    /**
     * Activity target type getter.
     *
     * @return mixed The activity target type.
     */
    public function getTargetType();

    /**
     * Casts an activity to a string.
     *
     * @return string The string representation of an activity
     */
    public function toString();
}
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
 * In its simplest form, an activity consists of an actor, a verb, an an object, and a target. It tells the story of a
 * person performing an action on or with an object -- "Geraldine posted a photo to her album" or "John shared a video".
 * In most cases these components will be explicit, but they may also be implied.
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
     * Default format is '{actor} {action} {object} {title}'
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
     * Check if the activity actor still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function findActor();

    /**
     * Check if the activity object still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function findObject();

    /**
     * Checks if the activity target still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function findTarget();

    /**
     * Casts an activity to a string.
     *
     * @return string The string representation of an activity
     */
    public function toString();
}
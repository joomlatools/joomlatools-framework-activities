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
 * This interface provides an interface for creating objects following the JSON Activity Streams 1.0 specification.
 * It also extends the specification by providing an activity format setter and getter for consumers to be able to
 * render activities from JSON activity stream data.
 *
 * @see http://activitystrea.ms/specs/json/1.0/
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityInterface
{
    /**
     * Activity format getter.
     *
     * @return string The activity string format.
     */
    public function getActivityFormat();

    /**
     * Activity icon getter.
     *
     * @return ComActivitiesActivityMedialinkInterface|null The activity icon, null if the activity does not have an icon.
     */
    public function getActivityIcon();

    /**
     * Activity ID getter.
     *
     * @return string The activity ID.
     */
    public function getActivityId();

    /**
     * Activity published date getter.
     *
     * @return KDate The published date.
     */
    public function getActivityPublished();

    /**
     * Activity actor getter.
     *
     * @return ComActivitiesActivityObjectInterface The activity actor object.
     */
    public function getActivityActor();

    /**
     * Activity object getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity object, null if the activity does not have an object.
     */
    public function getActivityObject();

    /**
     * Activity target getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity target object, null if the activity does no have a target.
     */
    public function getActivityTarget();

    /**
     * Activity verb getter.
     *
     * @return string The activity verb.
     */
    public function getActivityVerb();
}
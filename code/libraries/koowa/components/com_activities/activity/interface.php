<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Interface.
 *
 * In its simplest form, an activity consists of an actor, a verb, an object, and optionally a target. It tells the
 * story of a person performing an action on or with an object -- "Geraldine posted a photo to her album" or "John
 * shared a video". In most cases these components will be explicit, but they may also be implied.
 *
 * @link http://activitystrea.ms/specs/json/1.0/#activity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityInterface
{
    /**
     * Activity format getter.
     *
     * An activity format consist on a template for rendering activity messages.
     *
     * @return string The activity string format.
     */
    public function getActivityFormat();

    /**
     * Activity icon getter.
     *
     * @return ComActivitiesActivityMedialinkInterface|null The activity icon, null if the activity does not have an icon.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See icon property.
     */
    public function getActivityIcon();

    /**
     * Activity ID getter.
     *
     * @return string The activity ID.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See id property.
     */
    public function getActivityId();

    /**
     * Activity published date getter.
     *
     * @return KDate The published date.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See published property.
     */
    public function getActivityPublished();

    /**
     * Activity actor getter.
     *
     * @return ComActivitiesActivityObjectInterface The activity actor object.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See actor property.
     */
    public function getActivityActor();

    /**
     * Activity object getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity object, null if the activity does not have an object.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See object property.
     */
    public function getActivityObject();

    /**
     * Activity target getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity target object, null if the activity does no have a target.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See target property.
     */
    public function getActivityTarget();

    /**
     * Activity verb getter.
     *
     * @return string The activity verb.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See verb property.
     */
    public function getActivityVerb();
}
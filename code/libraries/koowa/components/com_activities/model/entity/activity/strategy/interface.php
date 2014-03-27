<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Entity Strategy Interface
 *
 * Provides an interface for querying activity stream data from activity database rows.
 * Entities implementing this interface can also be casted to strings.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesModelEntityActivityStrategyInterface
{
    /**
     * Entity setter
     *
     * @param ComActivitiesModelEntityActivity $activity The activity entity.
     *
     * @return $this.
     */
    public function setEntity(ComActivitiesModelEntityActivity $activity);

    /**
     * Entity getter.
     *
     * @return ComActivitiesModelEntityActivity The activity entity
     */
    public function getEntity();

    /**
     * Tells if the activity object still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function objectExists();

    /**
     * Activity object URL getter.
     *
     * @return string|null The activity object URL, null if not linkable.
     */
    public function getObjectUrl();

    /**
     * Tells if the activity target still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function targetExists();

    /**
     * Activity target URL getter.
     *
     * @return string|null The activity target URL, null if not linkable.
     */
    public function getTargetUrl();

    /**
     * Tells if the activity has a target.
     *
     * @return boolean True if it has a target, false otherwise.
     */
    public function hasTarget();

    /**
     * Tells if the activity actor still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function actorExists();

    /**
     * Activity actor URL getter.
     *
     * @return string|null The activity actor URL, null if not linkable or reachable.
     */
    public function getActorUrl();

    /**
     * Activity icon getter.
     *
     * @return string The activity icon class.
     */
    public function getIcon();

    /**
     * Activity message getter.
     *
     * @return ComActivitiesMessageInterface The activity message object.
     */
    public function getMessage();

    /**
     * Object type getter.
     *
     * @return string The object type.
     */
    public function getObjectType();

    /**
     * Target type getter.
     *
     * @return string|null The target type, null if no target.
     */
    public function getTargetType();

    /**
     * Target id getter.
     *
     * @return string|null The id of the target, null if no target.
     */
    public function getTargetId();
}
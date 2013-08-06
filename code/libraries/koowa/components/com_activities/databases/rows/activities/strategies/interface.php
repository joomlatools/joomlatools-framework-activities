<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Activity database row strategy interface.
 *
 * Provides an interface for querying activity stream data from activity database rows. Database rows implementing this
 * interface can also be casted to strings.
 *
 * The components of
 *
 */
interface ComActivitiesDatabaseRowActivityStrategyInterface
{
    /**
     * Row setter
     *
     * @param ComActivitiesDatabaseRowActivity $activity The activity row object.
     *
     * @return $this.
     */
    public function setRow(ComActivitiesDatabaseRowActivity $row);

    /**
     * Row getter.
     *
     * @return ComActivitiesDatabaseRowActivity The activity row object.
     */
    public function getRow();

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
     * Casts an activity row object to a string.
     *
     * @param boolean $html Whether to output HTML or plain text.
     *
     * @return string The string representation of the activity row object.
     */
    public function toString($html = true);
}
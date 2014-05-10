<?php
/**
 * @package        Nooku_Components
 * @subpackage     Activities
 * @copyright      Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */

/**
 * Activity Entity Interface
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesModelEntityActivityInterface
{
    /**
     * Check if the activity object still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function hasObject();

    /**
     * Get the activity object URL
     *
     * @return string|null The activity object URL, null if not linkable.
     */
    public function getObjectUrl();

    /**
     * Get the activity object type
     *
     * @return string The object type.
     */
    public function getObjectType();

    /**
     * Checks if the activity target still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function hasTarget();

    /**
     * Get the activity target identifier
     *
     * @return string|null The identifier of the target, null if no target.
     */
    public function getTargetId();

    /**
     * Get the activity target URL
     *
     * @return string|null The activity target URL, null if not linkable.
     */
    public function getTargetUrl();

    /**
     * Get the activity target type
     *
     * @return string|null The target type, null if no target.
     */
    public function getTargetType();

    /**
     * Check if the activity actor still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function hasActor();

    /**
     * Get the activity actor URL
     *
     * @return string|null The activity actor URL, null if not linkable or reachable.
     */
    public function getActorUrl();

    /**
     * Get the activity icon
     *
     * @return string The activity icon class.
     */
    public function getIcon();

    /**
     * Get the activity message
     *
     * @return ComActivitiesMessageInterface The activity message object.
     */
    public function getMessage();

}
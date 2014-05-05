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
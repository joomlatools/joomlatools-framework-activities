<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Message Interface.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesMessageInterface
{
    /**
     * Get the message format
     *
     * Default format is '{actor} {action} {object} {title}'
     *
     * @return string The message format.
     */
    public function getFormat();

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
     * Get the actor activity message parameter configuration
     *
     * @param KObjectConfig $config The message parameter configuration object.
     */
    public function getActorParameter(KObjectConfig $config);

    /**
     * Get the action activity message parameter configuration.
     *
     * @param KObjectConfig $config The activity message parameter configuration object.
     */
    public function getActionParameter(KObjectConfig $config);

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
     * Get the object activity message parameter configuration.
     *
     * @param KObjectConfig $config The activity message parameter configuration object.
     */
    public function getObjectParameter(KObjectConfig $config);

    /**
     * Get the title activity message parameter configuration.
     *
     * @param KObjectConfig $config The activity message parameter configuration object.
     */
    public function getTitleParameter(KObjectConfig $config);

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
     * Get the message parameters
     *
     * @return array The message parameters.
     */
    public function getActivityParameters();

    /**
     * Get the message translator
     *
     * @return ComActivitiesMessageTranslatorInterface The message translator.
     */
    public function getTranslator();

    /**
     * Casts an activity message to string.
     *
     * @return string The string representation of an activity message.
     */
    public function toString();
}
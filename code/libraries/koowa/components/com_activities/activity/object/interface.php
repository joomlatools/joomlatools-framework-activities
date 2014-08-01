<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Object Interface
 *
 * @link http://activitystrea.ms/specs/json/1.0/#object
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityObjectInterface
{
    /**
     * Label setter.
     *
     * @param string $label The label.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setLabel($label);

    /**
     * Label getter.
     *
     * @return string The label.
     */
    public function getLabel();

    /**
     * Activity object name setter.
     *
     * Identifies the object using a human-readable and plain-text string. HTML markup MUST NOT be included.
     *
     * @param string $name The activity object name.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setObjectName($name);

    /**
     * Activity object name (actor, object, target, ...) getter.
     *
     * @return string The activity object name.
     */
    public function getObjectName();

    /**
     * Display name setter.
     *
     * @param string $name The display name.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See displayName property.
     */
    public function setDisplayName($name);

    /**
     * Display name getter.
     *
     * @return string|null The display name, null if the object does not have a display name property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See displayName property.
     */
    public function getDisplayName();

    /**
     * Attachments setter.
     *
     * @param array $attachments An array of ComActivitiesActivityObjectInterface objects.
     * @param bool  $merge Tells if attachments should be replaced or merged with current existing attachments.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See attachments property.
     */
    public function setAttachments(array $attachments, $merge = true);

    /**
     * Attachments getter.
     *
     * @return array An array of ComActivitiesActivityObjectInterface objects.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See attachments property.
     */
    public function getAttachments();

    /**
     * Author setter.
     *
     * @param ComActivitiesActivityObjectInterface|null $author The author.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See author property.
     */
    public function setAuthor($author);

    /**
     * Author getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The author, null if the object does not have an actor property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See author property.
     */
    public function getAuthor();

    /**
     * Content setter.
     *
     * @param string $content The content.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See content property.
     */
    public function setContent($content);

    /**
     * Content getter.
     *
     * @return string|null The content, null if the object does not have a content property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See content property.
     */
    public function getContent();

    /**
     * Downstream duplicates setter.
     *
     * @param array $duplicates An array of ComActivitiesActivityObjectInterface objects.
     * @param bool $merge Tells if downstream duplicates should be replaced or merged with current existing
     *                    downstream duplicates.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See downstreamDuplicates property.
     */
    public function setDownstreamDuplicates(array $duplicates, $merge = true);

    /**
     * Downstream duplicates getter.
     *
     * @return array An array of ComActivitiesActivityObjectInterface objects.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See downstreamDuplicates property.
     */
    public function getDownstreamDuplicates();

    /**
     * Id setter.
     *
     * @param string|null $id
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See id property.
     */
    public function setId($id);

    /**
     * Id getter.
     *
     * @return string|null The id, null if the object does not have an id property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See id property.
     */
    public function getId();

    /**
     * Image setter.
     *
     * @param ComActivitiesActivityMedialinkInterface|null $image The image.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See image property.
     */
    public function setImage($image);

    /**
     * Image getter.
     *
     * @return ComActivitiesActivityMedialinkInterface|null The image, null if the object does not have an image property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See image property.
     */
    public function getImage();

    /**
     * Object type setter.
     *
     * @param string|null $type The object type.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See objectType property.
     */
    public function setObjectType($type);

    /**
     * Object type getter.
     *
     * @return string|null The object type, null if the object does not have an object type property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See objectType property.
     */
    public function getObjectType();

    /**
     * Published date setter.
     *
     * @param KDate $date The published date.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See published property.
     */
    public function setPublished($date);

    /**
     * Published date getter.
     *
     * @return KDate|null The published date, null if the object does not have a published property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See published property.
     */
    public function getPublished();

    /**
     * Summary setter.
     *
     * @param mixed $summary The summary.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See summary property.
     */
    public function setSummary($summary);

    /**
     * Summary getter.
     *
     * @return string|null The summary, null if the object does not have a summary property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See summary property.
     */
    public function getSummary();

    /**
     * Updated date setter.
     *
     * @param KDate|null $date The updated date.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See updated property.
     */
    public function setUpdated($date);

    /**
     * Updated date getter.
     *
     * @return KDate|null The updated date, null if the object does not have an updated date property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See updated property.
     */
    public function getUpdated();

    /**
     * Upstream duplicates setter.
     *
     * @param array $duplicates An array of ComActivitiesActivityObjectInterface objects.
     * @param bool $merge Tells if upstream duplicates should be replaced or merged with current existing
     *                    upstream duplicates.
     *
     * @return ComActivitiesActivityObjectInterface
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See upstreamDuplicates property.
     */
    public function setUpstreamDuplicates(array $duplicates, $merge = true);

    /**
     * Upstream duplicates getter.
     *
     * @return array An array of ComActivitiesActivityObjectInterface objects.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See upstreamDuplicates property.
     */
    public function getUpstreamDuplicates();

    /**
     * Url setter.
     *
     * @param KHttpUrl|null $url
     *
     * @return KHttpUrl
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See url property.
     */
    public function setUrl($url);

    /**
     * Url getter.
     *
     * @return KHttpUrl|null The url, null if the object does not have a url property.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#object See url property.
     */
    public function getUrl();

    /**
     * Deleted status setter.
     *
     * @param bool $status The deleted status
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setDeleted($status);

    /**
     * Tells if the object has been deleted, i.e. no longer reachable or persisted.
     *
     * @return bool True if the object has been deleted, false otherwise.
     */
    public function isDeleted();

    /**
     * Attributes setter.
     *
     * @param array $attributes The attributes.
     * @param bool  $merge      Tells if attributes should be replaced or merged with current existing attributes.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setAttributes(array $attribs = array(), $merge = true);

    /**
     * Attributes getter.
     *
     * @return array The attributes.
     */
    public function getAttributes();
}
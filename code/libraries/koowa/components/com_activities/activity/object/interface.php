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
 * @see http://activitystrea.ms/specs/json/1.0/#object
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityObjectInterface
{
    /**
     * Activity object name setter.
     *
     * @param string $name The activity object name.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setName($name);

    /**
     * Activity object name (actor, object, target, ...) getter.
     *
     * @return string The activity object name.
     */
    public function getName();

    /**
     * Attachments setter.
     *
     * @param array $attachments An array of ComActivitiesActivityObjectInterface objects.
     * @param bool  $merge Tells if attachments should be replaced or merged with current existing attachments.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setAttachments(array $attachments, $merge = true);

    /**
     * Attachments getter.
     *
     * @return array An array of ComActivitiesActivityObjectInterface objects.
     */
    public function getAttachments();

    /**
     * Author setter.
     *
     * @param ComActivitiesActivityObjectInterface|null $author The author.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setAuthor($author);

    /**
     * Author getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The author, null if the object does not have an actor property.
     */
    public function getAuthor();

    /**
     * Content setter.
     *
     * @param string $content The content.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setContent($content);

    /**
     * Content getter.
     *
     * @return string|null The content, null if the object does not have a content property.
     */
    public function getContent();

    /**
     * Display name setter.
     *
     * @param string $name The display name.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setDisplayName($name);

    /**
     * Display name getter.
     *
     * @return string|null The display name, null if the object does not have a display name property.
     */
    public function getDisplayName();

    /**
     * Downstream duplicates setter.
     *
     * @param array $duplicates An array of ComActivitiesActivityObjectInterface objects.
     * @param bool $merge Tells if downstream duplicates should be replaced or merged with current existing
     *                    downstream duplicates.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setDownstreamDuplicates(array $duplicates, $merge = true);

    /**
     * Downstream duplicates getter.
     *
     * @return array An array of ComActivitiesActivityObjectInterface objects.
     */
    public function getDownstreamDuplicates();

    /**
     * Id setter.
     *
     * @param string|null $id
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setId($id);

    /**
     * Id getter.
     *
     * @return string|null The id, null if the object does not have an id property.
     */
    public function getId();

    /**
     * Image setter.
     *
     * @param ComActivitiesActivityMedialinkInterface|null $image The image.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setImage($image);

    /**
     * Image getter.
     *
     * @return ComActivitiesActivityMedialinkInterface|null The image, null if the object does not have an image property.
     */
    public function getImage();

    /**
     * Object type setter.
     *
     * @param string|null $type The object type.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setObjectType($type);

    /**
     * Object type getter.
     *
     * @return string|null The object type, null if the object does not have an object type property.
     */
    public function getObjectType();

    /**
     * Published date setter.
     *
     * @param KDate $date The published date.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setPublished($date);

    /**
     * Published date getter.
     *
     * @return KDate|null The published date, null if the object does not have a published property.
     */
    public function getPublished();

    /**
     * Summary setter.
     *
     * @param mixed $summary The summary.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setSummary($summary);

    /**
     * Summary getter.
     *
     * @return string|null The summary, null if the object does not have a summary property.
     */
    public function getSummary();

    /**
     * Updated date setter.
     *
     * @param KDate|null $date The updated date.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setUpdated($date);

    /**
     * Updated date getter.
     *
     * @return KDate|null The updated date, null if the object does not have an updated date property.
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
     */
    public function setUpstreamDuplicates(array $duplicates, $merge = true);

    /**
     * Upstream duplicates getter.
     *
     * @return array An array of ComActivitiesActivityObjectInterface objects.
     */
    public function getUpstreamDuplicates();

    /**
     * Url setter.
     *
     * @param string|null $url
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setUrl($url);

    /**
     * Url getter.
     *
     * @return string|null The url, null if the object does not have a url property.
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
     * Tells if the object has been deleted.
     *
     * @return bool True if the object has been deleted, false otherwise.
     */
    public function isDeleted();

    /**
     * Value setter.
     *
     * @param string|null $value The value.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setValue($value);

    /**
     * Value getter.
     *
     * @return string|null The value, null if the object has no value.
     */
    public function getValue();

    /**
     * Link setter.
     *
     * @param array $attribs The link attributes.
     * @param bool  $merge   Tells if link attributes should be replaced or merged with current existing link attributes.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function setLink(array $attribs = array(), $merge = true);

    /**
     * Link getter.
     *
     * @return array The link attributes.
     */
    public function getLink();

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

    /**
     * Tells if the object translatable or not.
     *
     * @return bool True if translatable, false otherwise.
     */
    public function isTranslatable();

    /**
     * Sets the translatable status of the object.
     *
     * @param bool $status True for setting it as translatable, false otherwise.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function translate($status = true);
}
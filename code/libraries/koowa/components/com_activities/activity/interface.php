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
     * Activity format setter.
     *
     * @param ComActivitiesActivityFormatInterface $format
     *
     * @return ComActivitiesActivityInterface
     */
    public function setFormat(ComActivitiesActivityFormatInterface $format);

    /**
     * Activity format getter.
     *
     * @return string The activity string format.
     */
    public function getFormat();

    /**
     * Activity content getter.
     *
     * @param string $string The activity content.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setContent($content);

    /**
     * Activity content getter.
     *
     * @return string|null The activity content, null if the activity does not have a content.
     */
    public function getContent();

    /**
     * Activity icon setter.
     *
     * @param ComActivitiesActivityMedialinkInterface $icon The activity icon.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setIcon(ComActivitiesActivityMedialinkInterface $icon);

    /**
     * Activity icon getter.
     *
     * @return ComActivitiesActivityMedialinkInterface|null The activity icon, null if the activity does not have an icon.
     */
    public function getIcon();

    /**
     * Activity ID setter.
     *
     * @param string $id The activity ID.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setId($id);

    /**
     * Activity ID getter.
     *
     * @return string The activity ID.
     */
    public function getId();

    /**
     * Activity published date setter.
     *
     * @param KDate $date The published date.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setPublished(KDate $date);

    /**
     * Activity published date getter.
     *
     * @return KDate The published date.
     */
    public function getPublished();

    /**
     * Activity title setter.
     *
     * @param string $title The activity title.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setTitle($title);

    /**
     * Activity title getter.
     *
     * @return string|null The activity title, null is the activity does not have a title.
     */
    public function getTitle();

    /**
     * Activity actor object setter.
     *
     * @param ComActivitiesActivityObjectInterface $actor The actor object.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setObjectActor(ComActivitiesActivityObjectInterface $actor);

    /**
     * Activity actor object getter.
     *
     * @return ComActivitiesActivityObjectInterface The actor object.
     */
    public function getObjectActor();

    /**
     * Activity object object setter.
     *
     * @param ComActivitiesActivityObjectInterface $object The activity object object.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setObjectObject(ComActivitiesActivityObjectInterface $object);

    /**
     * Activity object object getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The object object, null if the activity does not have an object.
     */
    public function getObjectObject();

    /**
     * Activity target object setter.
     *
     * @param ComActivitiesActivityObjectInterface $target The target object.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setObjectTarget(ComActivitiesActivityObjectInterface $target);

    /**
     * Activity target object getter.
     *
     * @return ComActivitiesActivityObjectInterface|null The target object, null if the activity does no have a target.
     */
    public function getObjectTarget();


    /**
     * Activity generator object setter.
     *
     * @param ComActivitiesActivityObjectInterface $generator The generator object.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setObjectGenerator(ComActivitiesActivityObjectInterface $generator);

    /**
     * Activity generator object getter.
     *
     * @return ComActivitiesActivityObjectInterface The generator object.
     */
    public function getObjectGenerator();

    /**
     * Activity generator object setter.
     *
     * @param ComActivitiesActivityObjectInterface $generator The generator object.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setObjectProvider(ComActivitiesActivityObjectInterface $generator);

    /**
     * Activity provider object getter.
     *
     * @return ComActivitiesActivityObjectInterface The provider object.
     */
    public function getObjectProvider();

    /**
     * Activity objects getter.
     *
     * @return Array An array of ComActivitiesActivityObjectInterface objects.
     */
    public function getObjects();

    /**
     * Activity verb setter.
     *
     * @param string $verb The activity verb.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setVerb($verb);

    /**
     * Activity verb getter.
     *
     * @return string The activity verb.
     */
    public function getVerb();

    /**
     * Activity story setter.
     *
     * The activity story is the plain-text natural-language title or headline for the activity.
     *
     * @param string $story The activity story.
     *
     * @return ComActivitiesActivityInterface
     */
    public function setStory($story);

    /**
     * Activity story getter.
     *
     * @return string The activity story.
     */
    public function getStory();
}
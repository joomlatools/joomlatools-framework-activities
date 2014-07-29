<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Stream Media Link Interface
 *
 * @see http://activitystrea.ms/specs/json/1.0/#media-link
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityMedialinkInterface
{
    /**
     * Duration setter.
     *
     * @param int $duration The duration.
     *
     * @return ComActivitiesActivityStreamMedialinkInterface
     */
    public function setDuration($duration);

    /**
     * Duration getter.
     *
     * @return int|null The duration, null if the media link does not have a duration property.
     */
    public function getDuration();

    /**
     * Height setter.
     *
     * @param int $height The height.
     *
     * @return ComActivitiesActivityStreamMedialinkInterface
     */
    public function setHeight($height);

    /**
     * Height getter.
     *
     * @return int|null The height, null if the media link does not have a height property.
     */
    public function getHeight();

    /**
     * Url setter.
     *
     * @param KHttpUrl $url The url.
     *
     * @return ComActivitiesActivityStreamMedialinkInterface
     */
    public function setUrl(KHttpUrl $url);

    /**
     * Url getter.
     *
     * @return KHttpUrl The url.
     */
    public function getUrl();

    /**
     * Width setter.
     *
     * @param int $width The width.
     *
     * @return ComActivitiesActivityStreamMedialinkInterface
     */
    public function setWidth($width);

    /**
     * Width getter.
     *
     * @return int|null The width, null if the media link does not have a width property.
     */
    public function getWidth();
}
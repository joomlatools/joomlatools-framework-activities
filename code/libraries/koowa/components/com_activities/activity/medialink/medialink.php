<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Stream Media Link
 *
 * @see http://activitystrea.ms/specs/json/1.0/#media-link
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityMedialink extends KObjectArray implements ComActivitiesActivityMedialinkInterface
{
    /**
     * Get Duration
     *
     * @return int|null The duration, null if the media link does not have a duration property.
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set Duration
     *
     * @param int $duration The duration
     * @return ComActivitiesActivityMedialink
     */
    public function setDuration($duration)
    {
        $this->duration = (int) $duration;
        return $this;
    }

    /**
     * Get Height
     *
     * @return int|null The height, null if the media link does not have a height property.
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set Height
     *
     * @param int $height The height.
     * @return ComActivitiesActivityMedialink
     */
    public function setHeight($height)
    {
        $this->height = (int) $height;
        return $this;
    }

    /**
     * Get Url
     *
     * @return KHttpUrl The url.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set Url
     *
     * @param KHttpUrl $url The url.
     * @return ComActivitiesActivityMedialink
     */
    public function setUrl(KHttpUrl $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get Width
     *
     * @return int|null The width, null if the media link does not have a width property.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width
     *
     * @param int $width The width.
     * @return ComActivitiesActivityMedialink
     */
    public function setWidth($width)
    {
        $this->width = (int) $width;
        return $this;
    }
}
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Stream Media Link
 *
 * @see http://activitystrea.ms/specs/json/1.0/#media-link
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityStreamMedialink extends KObjectConfigJson implements ComActivitiesActivityStreamMedialinkInterface
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->append(array('url' => ''));
    }

    public function setDuration($duration)
    {
        $this->duration = (int) $duration;
        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setHeight($height)
    {
        $this->height = (int) $height;
        return $this;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setUrl($url)
    {
        $this->url = (string) $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setWidth($width)
    {
        $this->width = (int) $width;
        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set a parameter property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $value = new KObjectConfigJson($value);
        }

        parent::set($name, $value);
    }
}
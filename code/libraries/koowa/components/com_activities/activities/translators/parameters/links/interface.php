<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
interface ComActivitiesActivityTranslatorParameterLinkInterface
{
    /**
     * Link URL setter.
     *
     * @param string $url The link URL.
     *
     * @return $this.
     */
    public function setUrl($url);

    /**
     * Link URL getter.
     *
     * @return string The link URL.
     */
    public function getUrl();

    /**
     * Link attributes setter.
     *
     * @param array $attributes The link attributes.
     *
     * @return $this.
     */
    public function setAttributes($attributes);

    /**
     * Attributes getter.
     *
     * @return array The link attributes.
     */
    public function getAttributes();

    /**
     * Resets the link data.
     *
     * @return boolean True on success, false otherwise.
     */
    public function reset();

    /**
     * Casts the link to a string.
     *
     * @param string $text The link's text.
     *
     * @return string The string representation of the link given its text.
     */
    public function toString($text);
}
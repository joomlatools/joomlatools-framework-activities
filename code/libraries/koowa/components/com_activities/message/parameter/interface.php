<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Parameter Interface
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesMessageParameterInterface extends KObjectHandlable
{
    /**
     * Get the parameter name
     *
     * A name uniquely identifies a parameter.
     *
     * @return string The parameter name
     */
    public function getName();

    /**
     * Get the parameter value
     *
     * @param mixed $value The parameter value.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setValue($value);

    /**
     * Set the parameter value
     *
     * @return string The parameter value.
     */
    public function getValue();

    /**
     * Set the parameter content
     *
     * @param string $content The parameter content.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setContent($content);

    /**
     * Get the parameter content
     *
     * @return string The parameter content.
     */
    public function getContent();

    /**
     * Set the URL
     *
     * @param string $url The parameter URL.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setUrl($url);

    /**
     * Get the URL
     *
     * @return string The parameter url.
     */
    public function getUrl();

    /**
     * Set the parameter link attributes
     *
     * @param array $attributes The parameter link attributes.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setLinkAttributes($attributes);

    /**
     * Get the parameter link attributes
     *
     * @return array The parameter attributes.
     */
    public function getLinkAttributes();

    /**
     * Set the parameter attributes
     *
     * @param array $attributes The parameter attributes.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setAttributes($attributes);

    /**
     * Get the parameter attributes
     *
     * @return array The parameter attributes.
     */
    public function getAttributes();

    /**
     * Tells if the parameter is linkable or not.
     *
     * @return bool
     */
    public function isLinkable();

    /**
     * Tells if the parameter is translatable.
     *
     * @return bool True if translatable, false otherwise.
     */
    public function isTranslatable();

    /**
     * Casts an activity parameter to string.
     *
     * @return string The string representation of an activity parameter.
     */
    public function toString();
}
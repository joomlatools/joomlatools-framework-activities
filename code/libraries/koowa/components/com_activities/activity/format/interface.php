<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Format Interface
 *
 * Provides an interface for describing text representations of activities so that consumers may render them
 * on any markup language, format, internationalization, etc.
 *
 * Provided as an extension of the JSON Activity Streams 1.0 specification.
 *
 * @see http://activitystrea.ms/specs/json/1.0/
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityFormatInterface
{
    /**
     * Format string setter.
     *
     * The format string is a text representation of the activity which may be used for rendering activity
     * messages. This string supports parameters which may be replaced by named placeholders in the string.
     *
     * @see ComActivitiesActivityFormatInterface::addParameter
     * @see ComActivitiesActivityFormatInterface::addParameters
     *
     * @return ComActivitiesActivityFormatInterface
     */
    public function setString($string);

    /**
     * Format string getter.
     *
     * @return string The format string.
     */
    public function getString();

    /**
     * Adds a parameter to the format.
     *
     * @param ComActivitiesActivityFormatParameterInterface $parameter
     *
     * @return ComActivitiesActivityFormatInterface
     */
    public function addParameter(ComActivitiesActivityFormatParameterInterface $parameter);

    /**
     * Adds parameters to the format.
     *
     * @param Traversable $parameters A Traversable object or array containing
     *                                ComActivitiesActivityFormatParameterInterface objects.
     *
     * @return ComActivitiesActivityFormatInterface
     */
    public function addParameters($parameters);

    /**
     * Format parameters getter.
     *
     * @return  An array containing ComActivitiesActivityFormatParameterInterface objects.
     */
    public function getParameters();
}
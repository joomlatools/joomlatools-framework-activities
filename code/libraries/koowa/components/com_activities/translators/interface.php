<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Translator Interface
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesTranslatorInterface
{
    /**
     * Translates an activity string.
     *
     * @param string                                      $string               The activity string.
     * @param ComActivitiesTranslatorParameterInterface[] $parameters           An optional array containing parameter
     *                                                                          objects.
     *
     * @return string The translated activity string.
     */
    public function translate($string, array $parameters = array());

    /**
     * Activity string parser.
     *
     * Identifies the components (such as parameters, words, etc.) of activity strings.
     *
     * @param string $string The activity string.
     *
     * @return array An associative array containing the activity string components.
     */
    public function parse($string);
}
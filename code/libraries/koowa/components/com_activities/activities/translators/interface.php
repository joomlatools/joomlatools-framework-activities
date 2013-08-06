<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
interface ComActivitiesActivityTranslatorInterface
{
    /**
     * Translates an activity string.
     *
     * @param string                                              $string       The activity string.
     * @param ComActivitiesActivityTranslatorParameterInterface[] $parameters   An optional array containing parameter
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
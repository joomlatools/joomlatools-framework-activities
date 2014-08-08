<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Translator Interface
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityTranslatorInterface
{
    /**
     * Translates an activity format string.
     *
     * @param       $string     The format string.
     * @param array $parameters An array containing format parameters.
     *
     * @return string Translated activity format string.
     */
    public function translate($string, array $parameters = array());
}
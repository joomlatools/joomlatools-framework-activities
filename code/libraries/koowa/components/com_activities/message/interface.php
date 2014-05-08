<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Message Interface.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesMessageInterface
{
    /**
     * Set the message variables
     *
     * @param ComActivitiesMessageVariableSetInterface $variables A set of message variables.
     * @return ComActivitiesMessageInterface
     */
    public function setVariables(ComActivitiesMessageVariableSetInterface $variables);

    /**
     * Get the message variables
     *
     * @return ComActivitiesMessageVariableSetInterface A set of message variables.
     */
    public function getVariables();

    /**
     * Set the message key
     *
     * @param string $key The message key.
     * @return ComActivitiesMessageInterface
     */
    public function setKey($key);

    /**
     * Get the message key
     *
     * @return string The message key.
     */
    public function getKey();

    /**
     * Set the message scripts
     *
     * @param string $scripts Scripts to be included with the message.
     * @return ComActivitiesMessageInterface
     */
    public function setScripts($scripts);

    /**
     * Get the message scripts
     *
     * @return string Scripts to be included with the message.
     */
    public function getScripts();

    /**
     * Set the message translator
     *
     * @param ComActivitiesMessageTranslatorInterface $translator The message translator.
     * @return ComActivitiesMessageInterface
     */
    public function setTranslator(ComActivitiesMessageTranslatorInterface $translator);

    /**
     * Get the message translator
     *
     * @return ComActivitiesMessageTranslatorInterface The message translator.
     */
    public function getTranslator();

    /**
     * Casts an activity message to string.
     *
     * @return string The string representation of an activity message.
     */
    public function toString();
}
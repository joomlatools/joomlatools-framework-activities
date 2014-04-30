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
 */
interface ComActivitiesMessageInterface
{
    /**
     * Message parameters set setter.
     *
     * @param ComActivitiesMessageParameterSetInterface $parameters A set of message parameters.
     *
     * @return ComActivitiesMessageInterface
     */
    public function setParameters(ComActivitiesMessageParameterSetInterface $parameters);

    /**
     * Message parameter set getter.
     *
     * @return ComActivitiesMessageParameterSetInterface A set of message parameters.
     */
    public function getParameters();

    /**
     * Message key setter.
     *
     * @param string $key The message key.
     *
     * @return ComActivitiesMessageInterface
     */
    public function setKey($key);

    /**
     * Message key getter.
     *
     * @return string The message key.
     */
    public function getKey();

    /**
     * Message scripts setter.
     *
     * @param string $scripts Scripts to be included with the message.
     *
     * @return ComActivitiesMessageInterface
     */
    public function setScripts($scripts);

    /**
     * Message scripts getter
     *
     * @return string Scripts to be included with the message.
     */
    public function getScripts();

    /**
     * Message translator setter.
     *
     * @param ComActivitiesMessageTranslatorInterface The message translator.
     *
     * @return ComActivitiesMessageInterface
     */
    public function setTranslator(ComActivitiesMessageTranslatorInterface $translator);

    /**
     * Message translator getter.
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
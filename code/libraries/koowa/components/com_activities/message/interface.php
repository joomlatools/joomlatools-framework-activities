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
     * @param ComActivitiesMessageParameterCollectionInterface $parameters A set of message parameters.
     *
     * @return $this
     */
    public function setParameters(ComActivitiesMessageParameterSetInterface $parameters);

    /**
     * Message parameter set getter.
     *
     * @return ComActivitiesMessageParameterSetInterface A set of message parameters.
     */
    public function getParameters();

    /**
     * Message string setter.
     *
     * @param string $string The message string.
     *
     * @return $this
     */
    public function setString($string);

    /**
     * Message string getter.
     *
     * @return string The message string.
     */
    public function getString();

    /**
     * Message scripts setter.
     *
     * @param string $scripts Scripts to be included with the message.
     *
     * @return $this
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
     * @return $this
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
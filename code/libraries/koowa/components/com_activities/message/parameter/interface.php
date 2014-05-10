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
     * Text setter.
     *
     * @param mixed $text The parameter text.
     *
     * @return $this.
     */
    public function setText($text);

    /**
     * Text getter.
     *
     * @return string The parameter text.
     */
    public function getText();

    /**
     * Content setter.
     *
     * @param string $content The parameter content.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setContent($content);

    /**
     * Content getter.
     *
     * @return string The parameter content.
     */
    public function getContent();

    /**
     * Label getter.
     *
     * A label uniquely identifies a parameter.
     *
     * @return string The parameter label.
     */
    public function getLabel();

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
     * Link attributes setter.
     *
     * @param array $attributes The parameter link attributes.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setLinkAttributes($attributes);

    /**
     * Get the link attributes
     *
     * @return array The parameter attributes.
     */
    public function getLinkAttributes();

    /**
     * Set the attributes
     *
     * @param array $attributes The parameter attributes.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setAttributes($attributes);

    /**
     * Get the attributes
     *
     * @return array The parameter attributes.
     */
    public function getAttributes();

    /**
     * Set the translator
     *
     * @param KTranslatorInterface $translator The parameter translator.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setTranslator(KTranslatorInterface $translator);

    /**
     * Get the translator
     *
     * @return KTranslatorInterface The parameter translator.
     */
    public function getTranslator();

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
}
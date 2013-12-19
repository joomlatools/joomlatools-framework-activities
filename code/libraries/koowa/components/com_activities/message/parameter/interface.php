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
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * Content getter.
     *
     * @return The parameter content.
     */
    public function getContent();

    /**
     * Translatable state setter.
     *
     * @param bool The parameter is made translatable if true, non-translatable if false.
     *
     * @return $this.
     */
    public function setTranslatable($state);

    /**
     * Tells if the parameter is translatable.
     *
     * @return bool True if translatable, false otherwise.
     */
    public function isTranslatable();

    /**
     * Label getter.
     *
     * A label uniquely identifies a parameter.
     *
     * @return string The parameter label.
     */
    public function getLabel();

    /**
     * URL setter.
     *
     * @param string $url The parameter URL.
     *
     * @return $this.
     */
    public function setUrl($url);

    /**
     * URL getter.
     *
     * @return string The parameter url.
     */
    public function getUrl();

    /**
     * Tells if the parameter is linkable or not.
     *
     * @return bool
     */
    public function isLinkable();

    /**
     * Link attributes setter.
     *
     * @param array $attributes The parameter link attributes.
     *
     * @return $this.
     */
    public function setLinkAttributes($attributes);

    /**
     * Link attributes getter.
     *
     * @return array The parameter attributes.
     */
    public function getLinkAttributes();

    /**
     * Attributes setter.
     *
     * @param array $attributes The parameter attributes.
     *
     * @return $this.
     */
    public function setAttributes($attributes);

    /**
     * Attributes getter.
     *
     * @return array The parameter attributes.
     */
    public function getAttributes();

    /**
     * Parameter translator setter.
     *
     * @param KTranslatorInterface $translator The parameter translator.
     *
     * @return $this
     */
    public function setTranslator(KTranslatorInterface $translator);

    /**
     * Parameter translator getter.
     *
     * @return KTranslatorInterface The parameter translator.
     */
    public function getTranslator();
}
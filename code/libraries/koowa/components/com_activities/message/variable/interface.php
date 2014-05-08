<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Variable Interface
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesMessageVariableInterface extends KObjectHandlable
{
    /**
     * Text setter.
     *
     * @param mixed $text The variable text.
     *
     * @return $this.
     */
    public function setText($text);

    /**
     * Text getter.
     *
     * @return string The variable text.
     */
    public function getText();

    /**
     * Content setter.
     *
     * @param string $content The variable content.
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * Content getter.
     *
     * @return string The variable content.
     */
    public function getContent();

    /**
     * Translatable state setter.
     *
     * @param bool $state The variable is made translatable if true, non-translatable if false.
     *
     * @return $this.
     */
    public function setTranslatable($state);

    /**
     * Tells if the variable is translatable.
     *
     * @return bool True if translatable, false otherwise.
     */
    public function isTranslatable();

    /**
     * Label getter.
     *
     * A label uniquely identifies a variable.
     *
     * @return string The variable label.
     */
    public function getLabel();

    /**
     * URL setter.
     *
     * @param string $url The variable URL.
     *
     * @return $this.
     */
    public function setUrl($url);

    /**
     * URL getter.
     *
     * @return string The variable url.
     */
    public function getUrl();

    /**
     * Tells if the variable is linkable or not.
     *
     * @return bool
     */
    public function isLinkable();

    /**
     * Link attributes setter.
     *
     * @param array $attributes The variable link attributes.
     *
     * @return $this.
     */
    public function setLinkAttributes($attributes);

    /**
     * Link attributes getter.
     *
     * @return array The variable attributes.
     */
    public function getLinkAttributes();

    /**
     * Attributes setter.
     *
     * @param array $attributes The variable attributes.
     *
     * @return $this.
     */
    public function setAttributes($attributes);

    /**
     * Attributes getter.
     *
     * @return array The variable attributes.
     */
    public function getAttributes();

    /**
     * Variable translator setter.
     *
     * @param KTranslatorInterface $translator The variable translator.
     *
     * @return $this
     */
    public function setTranslator(KTranslatorInterface $translator);

    /**
     * Variable translator getter.
     *
     * @return KTranslatorInterface The variable translator.
     */
    public function getTranslator();
}
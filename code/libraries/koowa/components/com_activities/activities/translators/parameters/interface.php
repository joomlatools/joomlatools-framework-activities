<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
interface ComActivitiesActivityTranslatorParameterInterface
{
    /**
     * Text setter.
     *
     * @param mixed $value The parameter text.
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
     * Translator setter.
     *
     * @param KTranslator $translator The parameter translator.
     *
     * @return $this.
     */
    public function setTranslator(KTranslator $translator);

    /**
     * Translator getter.
     *
     * @return KTranslator The parameter translator.
     */
    public function getTranslator();

    /**
     * Renderer setter.
     *
     * @param ComActivitiesActivityTranslatorParameterRendererInterface $renderer
     *
     * @return $this.
     */
    public function setRenderer(ComActivitiesActivityTranslatorParameterRendererInterface $renderer);

    /**
     * Renderer getter.
     *
     * @return ComActivitiesActivityTranslatorParameterRendererInterface The parameter renderer.
     */
    public function getRenderer();

    /**
     * Renders the parameter object.
     *
     * @return string The rendered parameter.
     */
    public function render();

    /**
     * Link setter.
     *
     * @param ComActivitiesActivityTranslatorParameterLinkInterface $link The parameter link.
     *
     * @return $this.
     */
    public function setLink(ComActivitiesActivityTranslatorParameterLinkInterface $link);

    /**
     * Link getter.
     *
     * @return ComActivitiesActivityTranslatorParameterLinkInterface The parameter link.
     */
    public function getLink();

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
}
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Parameter Class
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesMessageParameter extends KObject implements ComActivitiesMessageParameterInterface
{
    /**
     * The parameter label.
     *
     * @var string
     */
    protected $_label;

    /**
     * The parameter text.
     *
     * @var string
     */
    protected $_text;

    /**
     * Determines if the parameter is translatable (true) or not (false).
     *
     * @var boolean
     */
    protected $_translate;

    /**
     * The parameter attributes.
     *
     * @var array
     */
    protected $_attributes;

    /**
     * The parameter link attributes.
     *
     * @var array
     */
    protected $_link_attributes;

    /**
     * The parameter url.
     *
     * @var string
     */
    protected $_url;

    /**
     * The parameter content.
     *
     * This property contains the formatted text that is be used for rendering activity messages.
     *
     * @var string
     */
    protected $_content;

    /**
     * The variable translator.
     *
     * @var mixed
     */
    protected $_translator;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!$config->label) {
            throw new InvalidArgumentException('A translator parameter must have a label');
        }

        $this->_label      = $config->label;
        $this->_translator = $config->translator;
        $this->_translate  = $config->translate;

        $this->setAttributes(KObjectConfig::unbox($config->attributes));
        $this->setLinkAttributes(KObjectConfig::unbox($config->link_attributes));

        $this->setText($config->text);
        $this->setContent($config->content);
        $this->setUrl($config->url);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'translator'      => 'com:activities.translator',
            'translate'       => false,
            'link_attributes' => array(),
            'attributes'      => array('class' => array('parameter'))
        ));

        parent::_initialize($config);
    }

    /**
     * Text setter.
     *
     * @param mixed $text The parameter text.
     * @return ComActivitiesMessageParameter
     */
    public function setText($text)
    {
        $this->_text = (string) $text;
        return $this;
    }

    /**
     * Text getter.
     *
     * @return string The parameter text.
     */
    public function getText()
    {
        $text = $this->_text;

        if ($this->isTranslatable()) {
            $text = $this->getTranslator()->translate($text);
        }

        return $text;
    }

    /**
     * Content setter.
     *
     * @param string $content The parameter content.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Content getter.
     *
     * @return string The parameter content.
     */
    public function getContent()
    {
        if (!$content = $this->_content) {
            $content = $this->getText();
        }

        return $content;
    }

    /**
     * Label getter.
     *
     * A label uniquely identifies a parameter.
     *
     * @return string The parameter label.
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Set the URL
     *
     * @param string $url The parameter URL.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setUrl($url)
    {
        $this->_url = (string) $url;
        return $this;
    }

    /**
     * Get the URL
     *
     * @return string The parameter url.
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set the attributes
     *
     * @param array $attributes The parameter attributes.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * Get the attributes
     *
     * @return array The parameter attributes.
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Link attributes setter.
     *
     * @param array $attributes The parameter link attributes.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setLinkAttributes($attributes)
    {
        $this->_link_attributes = $attributes;
        return $this;
    }

    /**
     * Get the link attributes
     *
     * @return array The parameter attributes.
     */
    public function getLinkAttributes()
    {
        return $this->_link_attributes;
    }

    /**
     * Set the translator
     *
     * @param KTranslatorInterface $translator The parameter translator.
     * @return ComActivitiesMessageParameterInterface
     */
    public function setTranslator(KTranslatorInterface $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Get the translator
     *
     * @return KTranslatorInterface The parameter translator.
     */
    public function getTranslator()
    {
        if (!$this->_translator instanceof KTranslatorInterface) {
            $this->setTranslator($this->getObject($this->_translator));
        }

        return $this->_translator;
    }

    /**
     * Tells if the parameter is translatable.
     *
     * @return bool True if translatable, false otherwise.
     */
    public function isTranslatable()
    {
        return (bool) $this->_translate;
    }

    /**
     * Tells if the parameter is linkable or not.
     *
     * @return bool
     */
    public function isLinkable()
    {
        return (bool) $this->getUrl();
    }
}
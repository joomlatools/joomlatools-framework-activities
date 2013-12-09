<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Default Activity Parameter Translator
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesTranslatorParameter extends KObject implements ComActivitiesTranslatorParameterInterface
{
    /**
     * @var string The parameter label.
     */
    protected $_label;

    /**
     * @var mixed The parameter translator.
     */
    protected $_translator;

    /**
     * @var string The parameter text.
     */
    protected $_text;

    /**
     * @var boolean Determines if the parameter is translatable (true) or not (false).
     */
    protected $_translate;

    /**
     * @var array The parameter attributes.
     */
    protected $_attributes;

    /**
     * @var array The parameter link attributes.
     */
    protected $_link_attributes;

    /**
     * @var string The parameter url.
     */
    protected $_url;

    /**
     * @var mixed The parameter renderer.
     */
    protected $_renderer;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!$config->label) {
            throw new InvalidArgumentException('A translator parameter must have a label');
        }

        $this->_label      = $config->label;
        $this->_renderer   = $config->renderer;
        $this->_translator = $config->translator;

        $this->setAttributes(KObjectConfig::unbox($config->attributes));
        $this->setLinkAttributes(KObjectConfig::unbox($config->link_attributes));
        $this->setTranslatable($config->translate);
        $this->setText($config->text);
        $this->setUrl($config->url);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'html'            => true,
            'translate'       => false,
            'link_attributes' => array(),
            'attributes'      => array('class' => array('parameter')),
            'translator'      => 'com:activities.translator',
        ))->append(array(
                   'renderer' => 'com:activities.translator.parameter.renderer.' . ($config->html ? 'html' : 'text')));

        parent::_initialize($config);
    }

    public function setTranslatable($state)
    {
        $this->_translate = (bool) $state;
        return $this;
    }

    public function setText($text)
    {
        $this->_text = (string) $text;
        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function isTranslatable()
    {
        return (bool) $this->_translate;
    }

    public function setTranslator(KTranslator $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    public function getTranslator()
    {
        if (!$this->_translator instanceof KTranslator)
        {
            $this->setTranslator($this->getObject($this->_translator));
        }
        return $this->_translator;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function setRenderer(ComActivitiesTranslatorParameterRendererInterface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    public function getRenderer()
    {
        if (!$this->_renderer instanceof ComActivitiesTranslatorParameterRendererInterface) {
            $this->setRenderer($this->getObject($this->_renderer));
        }

        return $this->_renderer;
    }

    public function render()
    {
        return (string) $this->getRenderer()->render($this);
    }

    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * @see ComActivitiesTranslatorParameterInterface::setLinkAttributes()
     */
    public function setLinkAttributes($attributes)
    {
        $this->_link_attributes = $attributes;
        return $this;
    }

    public function getLinkAttributes()
    {
        return $this->_link_attributes;
    }

    public function setUrl($url)
    {
        $this->_url = (string) $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function isLinkable()
    {
        return (bool) $this->getUrl();
    }
}

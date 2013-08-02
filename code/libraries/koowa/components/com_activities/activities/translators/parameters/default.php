<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComActivitiesActivityTranslatorParameterDefault extends KObject implements ComActivitiesActivityTranslatorParameterInterface
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
     * @var mixed The parameter renderer.
     */
    protected $_renderer;

    /**
     * @var mixed The parameter link.
     */
    protected $_link;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if (!$config->label)
        {
            throw new InvalidArgumentException('A translator parameter must have a label');
        }

        $this->_label      = $config->label;
        $this->_renderer   = $config->renderer;
        $this->_link       = $config->link;
        $this->_translator = $config->translator;

        $this->setAttributes(KConfig::unbox($config->attributes));
        $this->setTranslatable($config->translate);
        $this->setText($config->text);
    }

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'html'       => true,
            'link'       => 'com://admin/activities.activity.translator.parameter.link.html',
            'translate'  => false,
            'attributes' => array('class' => array('parameter')),
            'translator' => 'com://admin/activities.translator',
        ))->append(array(
                'renderer' => 'com://admin/activities.activity.translator.parameter.renderer.' . ($config->html ? 'html' : 'text')));

        parent::_initialize($config);
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::setTranslatable()
     */
    public function setTranslatable($state)
    {
        $this->_translate = (bool) $state;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::setText()
     */
    public function setText($text)
    {
        $this->_text = (string) $text;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::getText()
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::isTranslatable()
     */
    public function isTranslatable()
    {
        return (bool) $this->_translate;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::setTranslator()
     */
    public function setTranslator(KTranslator $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::getTranslator()
     */
    public function getTranslator()
    {
        if (!$this->_translator instanceof KTranslator)
        {
            $this->setTranslator($this->getService($this->_translator));
        }
        return $this->_translator;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::getLabel()
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::setRenderer()
     */
    public function setRenderer(ComActivitiesActivityTranslatorParameterRendererInterface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::getRenderer()
     */
    public function getRenderer()
    {
        if (!$this->_renderer instanceof ComActivitiesActivityTranslatorParameterRendererInterface)
        {
            $this->setRenderer($this->getService($this->_renderer));
        }

        return $this->_renderer;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::render()
     */
    public function render()
    {
        return (string) $this->getRenderer()->render($this);
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::setAttributes()
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::getAttributes()
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::setLink()
     */
    public function setLink(ComActivitiesActivityTranslatorParameterLinkInterface $link)
    {
        $this->_link = $link;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterInterface::getLink()
     */
    public function getLink()
    {
        if (!$this->_link instanceof ComActivitiesActivityTranslatorParameterLinkInterface)
        {
            $this->setLink($this->getService($this->_link));
        }

        return $this->_link;
    }
}

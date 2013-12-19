<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Parameter Class.
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
     * The parameter translator.
     *
     * @var mixed
     */
    protected $_translator;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!$config->label)
        {
            throw new InvalidArgumentException('A translator parameter must have a label');
        }

        $this->_label      = $config->label;
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
            'translator'      => 'com:activities.translator',
            'translate'       => false,
            'link_attributes' => array(),
            'attributes'      => array('class' => array('parameter'))
        ));

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
        $text = $this->_text;

        if ($this->isTranslatable())
        {
            $text = $this->getTranslator()->translate($text);
        }

        return $text;
    }

    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function isTranslatable()
    {
        return (bool) $this->_translate;
    }

    public function getLabel()
    {
        return $this->_label;
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

    public function setTranslator(KTranslatorInterface $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    public function getTranslator()
    {
        if (!$this->_translator instanceof KTranslatorInterface)
        {
            $this->setTranslator($this->getObject($this->_translator));
        }

        return $this->_translator;
    }
}
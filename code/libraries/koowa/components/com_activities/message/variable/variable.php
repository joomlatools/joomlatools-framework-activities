<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Variable Class.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesMessageVariable extends KObject implements ComActivitiesMessageVariableInterface
{
    /**
     * The variable label.
     *
     * @var string
     */
    protected $_label;

    /**
     * The variable text.
     *
     * @var string
     */
    protected $_text;

    /**
     * Determines if the variable is translatable (true) or not (false).
     *
     * @var boolean
     */
    protected $_translate;

    /**
     * The variable attributes.
     *
     * @var array
     */
    protected $_attributes;

    /**
     * The variable link attributes.
     *
     * @var array
     */
    protected $_link_attributes;

    /**
     * The variable url.
     *
     * @var string
     */
    protected $_url;

    /**
     * The variable content.
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
            throw new InvalidArgumentException('A translator variable must have a label');
        }

        $this->_label      = $config->label;
        $this->_translator = $config->translator;

        $this->setAttributes(KObjectConfig::unbox($config->attributes));
        $this->setLinkAttributes(KObjectConfig::unbox($config->link_attributes));
        $this->setTranslatable($config->translate);
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
            'attributes'      => array('class' => array('variable'))
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

        if ($this->isTranslatable()) {
            $text = $this->getTranslator()->translate($text);
        }

        return $text;
    }

    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Content getter will return text as content if no content is set.
     */
    public function getContent()
    {
        if (!$content = $this->_content) {
            $content = $this->getText();
        }

        return $content;
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
        if (!$this->_translator instanceof KTranslatorInterface) {
            $this->setTranslator($this->getObject($this->_translator));
        }

        return $this->_translator;
    }
}
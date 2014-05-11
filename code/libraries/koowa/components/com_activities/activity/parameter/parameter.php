<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Parameter Class
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityParameter extends KObject implements ComActivitiesActivityParameterInterface
{
    /**
     * The parameter name
     *
     * @var string
     */
    protected $__name;

    /**
     * The parameter value.
     *
     * @var string
     */
    protected $_value;

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
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (empty($config->name)) {
            throw new InvalidArgumentException('A message parameter must have a name');
        }

        $this->__name      = $config->name;
        $this->_translate  = $config->translate;

        $this->setAttributes(KObjectConfig::unbox($config->attributes));
        $this->setLinkAttributes(KObjectConfig::unbox($config->link_attributes));

        $this->setValue($config->value);
        $this->setContent($config->content);
        $this->setUrl($config->url);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'name'            => null,
            'value'           => '',
            'translate'       => false,
            'link_attributes' => array(),
            'attributes'      => array('class' => array('parameter'))
        ));

        parent::_initialize($config);
    }

    /**
     * Get the parameter name
     *
     * A name uniquely identifies a parameter.
     *
     * @return string The parameter name
     */
    public function getName()
    {
        return $this->__name;
    }

    /**
     * Get the parameter value
     *
     * @param mixed $value The parameter value.
     * @return ComActivitiesActivityParameterInterface
     */
    public function setValue($value)
    {
        $this->_value = (string) $value;
        return $this;
    }

    /**
     * Set the parameter value
     *
     * @return string The parameter value.
     */
    public function getValue()
    {
        $value = $this->_value;

        if ($this->isTranslatable()) {
            $text = $this->getObject('translator')->translate($value);
        }

        return $value;
    }

    /**
     * Set the parameter content
     *
     * @param string $content The parameter content.
     * @return ComActivitiesActivityParameterInterface
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Get the parameter content
     *
     * @return string The parameter content.
     */
    public function getContent()
    {
        if (!$content = $this->_content) {
            $content = $this->getValue();
        }

        return $content;
    }

    /**
     * Set the URL
     *
     * @param string $url The parameter URL.
     * @return ComActivitiesActivityParameterInterface
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
     * @return ComActivitiesActivityParameterInterface
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
     * @return ComActivitiesActivityParameterInterface
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

    /**
     * Casts an activity parameter to string.
     *
     * @return string The string representation of an activity parameter.
     */
    public function toString()
    {
        return $this->getContent();
    }

    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
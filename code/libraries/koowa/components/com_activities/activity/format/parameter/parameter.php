<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Format Parameter Class
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityFormatParameter extends KObjectConfig implements ComActivitiesActivityFormatParameterInterface
{
    /**
     * The parameter name
     *
     * @var string
     */
    private $__name;

    /**
     * The parameter format.
     *
     * @var string
     */
    protected $_format;

    /**
     * Constructor.
     *
     * @param    string             $name   The parameter name
     * @param   array|KObjectConfig $config An associative array of configuration settings or a KObjectConfig instance.
     */
    public function __construct( $name, $config = array())
    {
        parent::__construct($config);

        $this->append(array(
            'translate' => true,
            'value'     => '',
            'link'      => array(
                'attribs' => array()
            ),
            'attribs'   => array(
                'class' => array('parameter')
            )
        ));

        //Set the parameter name.
        $this->__name = $name;
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
        $this->value = (string) $value;
        return $this;
    }

    /**
     * Set the parameter value
     *
     * @return string The parameter value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the attributes
     *
     * @param array $attributes The parameter attributes.
     * @return ComActivitiesActivityParameterInterface
     */
    public function setAttributes($attributes)
    {
        $this->attribs = $attributes;
        return $this;
    }

    /**
     * Get the attributes
     *
     * @return array The parameter attributes.
     */
    public function getAttributes()
    {
        return $this->attribs;
    }

    /**
     * Set the link attributes
     *
     * @param array $attributes The link attributes.
     * @return ComActivitiesActivityParameterInterface
     */
    public function setLink($attributes)
    {
        $this->link = $attributes;
        return $this;
    }

    /**
     * Get the link attributes
     *
     * @return array The link attributes
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Tells if the parameter is linkable or not.
     *
     * @return bool
     */
    public function isLinkable()
    {
        return (bool) $this->getLink()->href;
    }

    /**
     * Tells if the parameter is translatable or not.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return (bool) $this->translate;
    }

    public function translate($status = true)
    {
        $this->translate = (bool) $status;
        return $this;
    }

    /**
     * Set a parameter property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $value = new KObjectConfig($value);
        }

        parent::set($name, $value);
    }
}
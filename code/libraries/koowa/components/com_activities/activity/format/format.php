<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Format Class
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityFormat extends KObject implements ComActivitiesActivityFormatInterface
{
    /**
     * @var string The format string.
     */
    protected $_string;

    /**
     * @var array The format parameters.
     */
    protected $_parameters;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_parameters = array();

        $this->setString($config->string);
        $this->addParameters($config->parameters);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('parameters' => array(), 'attribs' => array()));
        parent::_initialize($config);
    }

    public function setString($string)
    {
        $this->_string = (string) $string;
        return $this;
    }

    public function getString()
    {
        return $this->_string;
    }

    public function addParameter(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $this->_parameters[$parameter->getName()] = $parameter;

        return $this;
    }

    public function addParameters($parameters)
    {
        foreach ($parameters as $parameter)
        {
            $this->addParameter($parameter);
        }

        return $this;
    }

    public function getParameters()
    {
        return $this->_parameters;
    }
}
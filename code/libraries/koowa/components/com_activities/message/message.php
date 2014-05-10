<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Message Class.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesMessage extends KObject implements ComActivitiesMessageInterface
{
    /**
     * The message translator.
     *
     * @var mixed
     */
    protected $_translator;

    /**
     * The message format.
     *
     * @var string
     */
    protected $_format;

    /**
     * The message parameters.
     *
     * @var ComActivitiesMessageParametersInterface
     */
    protected $_parameters;

    /**
     * The message scripts.
     *
     * @var string
     */
    protected $_scripts;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setFormat($config->format);
        $this->setScripts($config->scripts);

        $this->_parameters = $config->parameters;
        $this->_translator = $config->translator;
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
            'format'     => '{actor} {action} {object} {title}',
            'parameters' => 'com:activities.message.parameters',
            'translator' => 'com:activities.message.translator'
        ));

        parent::_initialize($config);
    }

    /**
     * Set the message format
     *
     * @param string $format The message format.
     * @return ComActivitiesMessageInterface
     */
    public function setFormat($format)
    {
        $this->_format = (string) $format;
        return $this;
    }

    /**
     * Get the message format
     *
     * @return string The message format.
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Set the message parameters
     *
     * @param ComActivitiesMessageParametersInterface $parameters The message parameters.
     * @return ComActivitiesMessageInterface
     */
    public function setParameters(ComActivitiesMessageParametersInterface $parameters)
    {
        $this->_parameters = $parameters;
        return $this;
    }

    /**
     * Get the message parameters
     *
     * @return ComActivitiesMessageParametersInterface The message parameters.
     */
    public function getParameters()
    {
        if (!$this->_parameters instanceof ComActivitiesMessageParametersInterface) {
            $this->setParameters($this->getObject($this->_parameters));
        }

        return $this->_parameters;
    }

    /**
     * Set the message scripts
     *
     * @param string $scripts Scripts to be included with the message.
     * @return ComActivitiesMessageInterface
     */
    public function setScripts($scripts)
    {
        $this->_scripts = (string) $scripts;
        return $this;
    }

    /**
     * Get the message scripts
     *
     * @return string Scripts to be included with the message.
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * Set the message translator
     *
     * @param ComActivitiesMessageTranslatorInterface $translator The message translator.
     * @return ComActivitiesMessageInterface
     */
    public function setTranslator(ComActivitiesMessageTranslatorInterface $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Get the message translator
     *
     * @return ComActivitiesMessageTranslatorInterface The message translator.
     */
    public function getTranslator()
    {
        if (!$this->_translator instanceof ComActivitiesMessageTranslatorInterface) {
            $this->setTranslator($this->getObject($this->_translator));
        }

        return $this->_translator;
    }

    /**
     * Casts an activity message to string.
     *
     * @return string The string representation of an activity message.
     */
    public function toString()
    {
        return $this->getTranslator()->translateMessage($this);
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
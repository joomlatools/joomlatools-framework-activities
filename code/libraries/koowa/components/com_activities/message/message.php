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

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setFormat($config->format);
        $this->setScripts($config->scripts);

        $this->_parameters = $config->parameters;
        $this->_translator = $config->translator;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'format'     => '{actor} {action} {object} {title}',
            'parameters' => 'com:activities.message.parameters',
            'translator' => 'com:activities.message.translator'
        ));

        parent::_initialize($config);
    }

    public function setFormat($format)
    {
        $this->_format = (string) $format;
        return $this;
    }

    public function getFormat()
    {
        return $this->_format;
    }

    public function setParameters(ComActivitiesMessageParametersInterface $parameters)
    {
        $this->_parameters = $parameters;
        return $this;
    }

    public function getParameters()
    {
        if (!$this->_parameters instanceof ComActivitiesMessageParametersInterface) {
            $this->setParameters($this->getObject($this->_parameters));
        }

        return $this->_parameters;
    }

    public function setScripts($scripts)
    {
        $this->_scripts = (string) $scripts;
        return $this;
    }

    public function getScripts()
    {
        return $this->_scripts;
    }

    public function setTranslator(ComActivitiesMessageTranslatorInterface $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    public function getTranslator()
    {
        if (!$this->_translator instanceof ComActivitiesMessageTranslatorInterface) {
            $this->setTranslator($this->getObject($this->_translator));
        }

        return $this->_translator;
    }

    public function toString()
    {
        return $this->getTranslator()->translateMessage($this);
    }
}
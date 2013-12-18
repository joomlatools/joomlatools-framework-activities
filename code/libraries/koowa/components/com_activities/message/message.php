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
 */
class ComActivitiesMessage extends KObject implements ComActivitiesMessageInterface
{
    /**
     * The message string.
     *
     * @var string
     */
    protected $_string;

    /**
     * The set of message parameters.
     *
     * @var ComActivitiesMessageParameterSetInterface
     */
    protected $_parameters;

    /**
     * The message scripts.
     *
     * @var string
     */
    protected $_scripts;

    /**
     * The message translator.
     *
     * @var mixed
     */
    protected $_translator;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setString($config->string);
        $this->setScripts($config->scripts);

        $this->_parameters = $config->parameters;
        $this->_translator = $config->translator;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'parameters' => 'com:activities.message.parameter.set',
            'translator' => 'com:activities.message.translator'));
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

    public function setParameters(ComActivitiesMessageParameterSetInterface $parameters)
    {
        $this->_parameters = $parameters;
        return $this;
    }

    public function getParameters()
    {
        if (!$this->_parameters instanceof ComActivitiesMessageParameterSetInterface)
        {
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
        if (!$this->_translator instanceof ComActivitiesMessageTranslatorInterface)
        {
            $this->setTranslator($this->getObject($this->_translator));
        }

        return $this->_translator;
    }

    public function toString()
    {
        return $this->getTranslator()->translateMessage($this);
    }
}
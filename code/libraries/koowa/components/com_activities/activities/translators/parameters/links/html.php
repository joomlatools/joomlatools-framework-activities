<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComActivitiesActivityTranslatorParameterLinkHtml extends KObject implements ComActivitiesActivityTranslatorParameterLinkInterface
{
    /**
     * @var string The link URL.
     */
    protected $_url;

    /**
     * @var array Associative array containing the link attributes.
     */
    protected $_attributes;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->setUrl((string) $config->url);
        $this->setAttributes(KConfig::unbox($config->attributes));
    }

    protected function _initialize(KConfig $config)
    {
        $config->append(array('attributes' => array()));
        parent::_initialize($config);
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterLinkInterface::setUrl()
     */
    public function setUrl($url)
    {
        $this->_url = (string) $url;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterLinkInterface::getUrl()
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterLinkInterface::setAttributes()
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterLinkInterface::getAttributes()
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterLinkInterface::reset()
     */
    public function reset()
    {
        $this->_url        = null;
        $this->_attributes = null;

        return true;
    }

    /**
     * @see ComActivitiesActivityTranslatorParameterLinkInterface::toString()
     */
    public function toString($text)
    {
        if ($url = $this->getUrl())
        {
            $url = htmlspecialchars($url, ENT_QUOTES);
        }
        else
        {
            $url = '#';
        }

        $attributes = $this->getAttributes();

        return '<a ' . (empty($attributes) ? '' : KHelperArray::toString($attributes)) . ' href="' . $url . '">' . (string) $text . '</a>';
    }
}
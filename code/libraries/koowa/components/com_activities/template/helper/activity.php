<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesTemplateHelperActivity extends KTemplateHelperAbstract implements KObjectMultiton
{
    /**
     * Holds a list of loaded scripts.
     *
     * @var bool
     */
    static protected $_scripts_loaded;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        self::$_scripts_loaded = array();
    }

    /**
     * Renders an activity message.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The rendered activity.
     * @throws InvalidArgumentException
     */
    public function message($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array('format' => 'html'));

        $renderer = '_render' . ucfirst($config->format);

        if (!method_exists($this, $renderer)) {
            throw new InvalidArgumentException('Renderer not found');
        }

        $entity = $config->entity;

        if (!$entity instanceof ComActivitiesModelEntityActivity) {
            throw new InvalidArgumentException('Activity entity not found');
        }

        return $this->$renderer($entity);
    }

    /**
     * Activity message Html renderer.
     *
     * @param ComActivitiesActivityInterface $message The activity message.
     * @return string The Html message.
     */
    protected function _renderHtml(ComActivitiesActivityInterface $message)
    {
        //Render activity parameters
        foreach ($message->getActivityParameters() as $parameter)
        {
            $output = '<span class="text">' . $parameter->getValue() . '</span>';

            if ($parameter->isLinkable())
            {
                $link_attributes = $parameter->getLinkAttributes();

                $view       = $this->getTemplate()->getView();
                $url        = $view->getActivityRoute($parameter->getUrl());
                $attributes = !empty($link_attributes) ? $this->buildAttributes($link_attributes) : '';

                $output = '<a ' . $attributes . ' href="' . $url . '">' . $output . '</a>';
            }

            $attribs = $parameter->getAttributes();

            if (count($attribs)) {
                $output = '<span ' . $this->buildAttributes($attribs) . '>' . $output . '</span>';
            }

            $parameter->setContent($output);
        }

        //Render activity message
        $html = '';
        $html .= $message->toString();

        //Append scripts
        $identifier = (string) $message->getIdentifier();
        if (!in_array($identifier, self::$_scripts_loaded))
        {
            if ($scripts = $message->getScripts()) {
                $html .= $scripts;
            }

            self::$_scripts_loaded[] = $identifier;
        }

        return $html;
    }

    /**
     * Activity message text renderer.
     *
     * @param ComActivitiesActivityInterface $message The activity message.
     * @return string The text message.
     */
    protected function _renderText(ComActivitiesActivityInterface $message)
    {
        return $message->toString();
    }
}

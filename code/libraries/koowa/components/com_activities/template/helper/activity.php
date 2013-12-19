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

        if (!method_exists($this, $renderer))
        {
            throw new InvalidArgumentException('Renderer not found');
        }

        $row = $config->row;

        if (!$row instanceof ComActivitiesDatabaseRowActivity)
        {
            throw new InvalidArgumentException('Activity row not found');
        }

        return $this->$renderer($row->getStrategy()->getMessage());
    }

    /** Activity message Html renderer.
     *
     * @param ComActivityMessageInterface $message The activity message.
     *
     * @return string The Html message.
     */
    protected function _renderHtml(ComActivitiesMessageInterface $message)
    {
        foreach ($message->getParameters() as $parameter)
        {
            $output = '<span class="text">' . $parameter->getText() . '</span>';

            if ($parameter->isLinkable())
            {
                $link_attributes = $parameter->getLinkAttributes();

                $view = $this->getTemplate()->getView();

                $url = $view->getActivityRoute($parameter->getUrl());

                $output = '<a ' . (empty($link_attributes) ? '' : $this->buildAttributes($link_attributes)) . ' href="' . $url . '">' . $output . '</a>';
            }

            $attribs = $parameter->getAttributes();

            if (count($attribs))
            {
                $output = '<span ' . $this->buildAttributes($attribs) . '>' . $output . '</span>';
            }

            $parameter->setContent($output);
        }

        $html = $message->toString();
        $html .= $message->getScripts(); // Append scripts.

        return $html;
    }

    /**
     * Activity message text renderer.
     *
     * @param ComActivitiesMessageInterface $message The activity message.
     *
     * @return string The text message.
     */
    protected function _renderText(ComActivitiesMessageInterface $message)
    {
        foreach ($message->getParameters() as $parameter)
        {
            $parameter->setContent($parameter->getText());
        }

        return $message->toString();
    }
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesTemplateHelperActivity extends KTemplateHelperAbstract implements KObjectMultiton, ComActivitiesActivityRendererInterface
{
    /**
     * Renders an activity.
     *
     * Wraps around ::render for easy use on layouts.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The rendered activity.
     */
    public function activity($config = array())
    {
        $config = new KObjectConfig($config);

        $output = '';

        if ($activity = $config->entity) {
            $output = $this->render($activity, $config);
        }

        return $output;
    }

    /**
     * Renders an activity.
     *
     * @param ComActivitiesActivityInterface $activity The activity object.
     * @param  array                         $config   An optional configuration array.
     *
     * @return string The rendered activity.
     */
    public function render(ComActivitiesActivityInterface $activity, $config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'html' => true,
        ));

        $output = $activity->getActivityFormat();

        if (preg_match_all('/{(.*?)}/', $output, $replacements))
        {
            $objects = $activity->objects;

            foreach ($replacements[1] as $replacement)
            {
                $parts = explode(':', $replacement);

                if (isset($objects[$parts[0]]))
                {
                    $parameter = $objects[$parts[0]];

                    // Deal with context translations.
                    if (count($parts) === 2)
                    {
                        $parameter = clone $parameter;
                        $parameter->setValue($parts[1])->translate(false);
                    }

                    if (!$config->html)
                    {
                        $content = $parameter->getValue();

                        if ($parameter->isTranslatable()) {
                            $content = $this->translate($content);
                        }

                    }
                    else $content = $this->parameter(array('parameter' => $parameter));

                    $output = str_replace('{' . $replacement . '}', $content, $output);
                }
            }
        }

        return $output;
    }

    /**
     * Renders an HTML formatted activity format parameter.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The HTML formatted format parameter.
     */
    public function parameter($config = array())
    {
        $config = new KObjectConfig($config);

        $output = '';

        if ($parameter = $config->parameter)
        {
            $output = $parameter->getValue();

            if ($parameter->isTranslatable()) {
                $output = $this->translate($output);
            }

            if ($link = $parameter->getLink())
            {
                $view = $this->getTemplate()->getView();

                // Route link URL if any.
                if (isset($link['href'])) {
                    $link['href'] = $view->getActivityRoute($link['href']);
                }

                $attribs = $this->buildAttributes($link);

                $output = "<a {$attribs}>{$output}</a>";
            }

            $attribs = $parameter->getAttributes() ? $this->buildAttributes($parameter->getAttributes()) : '';

            $output = "<span {$attribs}>{$output}</span>";
        }

        return $output;
    }
}

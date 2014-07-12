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
class ComActivitiesTemplateHelperActivity extends KTemplateHelperAbstract implements KObjectMultiton
{
    /**
     * Renders an activity.
     *
     * @param  array $config An optional configuration array.
     *
     * @throws InvalidArgumentException
     * @return string The rendered activity
     */
    public function render($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'html' => true,
        ));

        $activity = $config->entity;

        if (!$activity instanceof ComActivitiesActivityInterface) {
            throw new InvalidArgumentException('Activity Not Found');
        }

        $output = $config->format;

        if (!$output) {
            $output = $this->format(array('entity' => $activity));
        }

        if (preg_match_all('/{(.*?)}/', $output, $replacements))
        {
            $objects = $activity->getActivityObjects();

            foreach($replacements[1] as $replacement)
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

                    $content = $parameter->getValue();

                    if ($config->html) {
                        $content = $this->parameter(array('parameter' => $parameter));
                    }

                    $output = str_replace('{'.$replacement.'}', $content, $output);
                }
            }
        }

        return $output;
    }

    /**
     * Renders an activity format.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The activity format.
     */
    public function format($config = array())
    {
        $config = new KObjectConfig($config);

        $activity = $config->entity;

        if (!$activity instanceof ComActivitiesActivityInterface) {
            throw new InvalidArgumentException('Activity Not Found');
        }

        $format = $activity->getActivityFormat();

        $parameters = array();

        if (preg_match_all('/\{(.*?)\}/', $format, $matches) !== false)
        {
            $objects = $activity->getActivityObjects();

            foreach ($matches[1] as $name)
            {
                if (isset($objects[$name]))
                {
                    $parameters[] = $objects[$name];
                }
            }
        }

        $translator = $this->getObject('com:activities.activity.translator');

        // Translate format to a readable translated string.
        return $translator->translate($translator->getOverride($format, $parameters));
    }

    /**
     * Renders an HTML formatted activity format parameter.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The HTML formatted format parameter.
     */
    public function parameter(array $config = array())
    {
        $config = new KObjectConfig($config);

        $parameter = $config->parameter;

        if (!$parameter instanceof ComActivitiesActivityObjectInterface)
        {
            throw new InvalidArgumentException('Parameter Not Found');
        }

        $output = $parameter->getValue();

        if ($parameter->isTranslatable()) {
            $output = $this->translate($output);
        }

        if ($parameter->getLink())
        {
            $view   = $this->getTemplate()->getView();
            $link = $parameter->getLink();

            // Route link URL if any.
            if (isset($link['href'])) {
                $link['href'] = $view->getActivityRoute($link['href']);
            }

            $attribs = $this->buildAttributes($parameter->getAttributes());

            $output = "<a {$attribs}>{$output}</a>";
        }

        $attribs = $parameter->getAttributes() ? $this->buildAttributes($parameter->getAttributes()) : '';

        $output = "<span {$attribs}>{$output}</span>";

        return $output;
    }
}

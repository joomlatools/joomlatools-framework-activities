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

        $output = $activity->getFormat()->getString();

        if (preg_match_all('/{(.*?)}/', $output, $replacements))
        {
            $parameters = $activity->getFormat()->getParameters();

            foreach($replacements[1] as $replacement)
            {
                $parts = explode(':', $replacement);

                if (isset($parameters[$parts[0]]))
                {
                    $parameter = $parameters[$parts[0]];

                    // Deal with context translations.
                    if (count($parts) === 2)
                    {
                        $parameter = clone $parameter;
                        $parameter->setValue($parts[1]);
                        $parameter->setTranslatable(false);
                    }

                    if ($config->html) {
                        $parameter = $this->_renderHtmlParameter($parameter);
                    } else {
                        $value     = $parameter->getValue();
                        $parameter = $parameter->isTranslatable() ? $value : $this->translate($value);
                    }

                    $output = str_replace('{'.$replacement.'}', $parameter, $output);
                }
            }
        }

        return $output;
    }

    /**
     * Renders an HTML formatted activity format parameter.
     *
     * @param ComActivitiesActivityFormatParameterInterface $parameter The activity format parameter.
     *
     * @return string The HTML formatted format parameter.
     */
    protected function _renderHtmlParameter(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $output = $parameter->getValue();

        if ($parameter->isTranslatable())
        {
            $output = $this->translate($output);
        }

        if ($parameter->isLinkable())
        {
            $link = $parameter->getLink();

            $view    = $this->getTemplate()->getView();
            $url     = $view->getActivityRoute($link->href);
            $attribs = !empty($link->attribs) ? $this->buildAttributes($link->attribs) : '';

            $output = "<a {$attribs} href=\"{$url}\">{$output}</a>";
        }

        $attribs = $parameter->getAttributes();

        if (count($attribs))
        {
            $attribs = $this->buildAttributes($attribs);
            $output  = "<span {$attribs}>{$output}</span>";
        }

        return $output;
    }
}

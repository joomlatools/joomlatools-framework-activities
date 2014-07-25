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

        if (preg_match_all('/{(.*?)}/', $output, $labels))
        {
            $tokens = $activity->tokens;

            foreach ($labels[1] as $label)
            {
                $parts = explode(':', $label);

                $label = $parts[0];

                if (isset($tokens[$label]))
                {
                    $object = $tokens[$label];

                    // Deal with context translations.
                    if (isset($parts[1]))
                    {
                        $object = clone $object;
                        $object->setDisplayName($parts[1]);
                    }

                    if (!$config->html) {

                        $content = $object->getDisplayName();
                    } else $content = $this->object(array('object' => $object));

                    $output = str_replace('{' . $label . '}', $content, $output);
                }
            }
        }

        return $output;
    }

    /**
     * Renders an HTML formatted activity object.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The HTML formatted object.
     */
    public function object($config = array())
    {
        $config = new KObjectConfig($config);

        $output = '';

        $object = $config->object;

        if ($object instanceof ComActivitiesActivityObjectInterface)
        {
            $output = $object->getDisplayName();

            $attribs = $object->getAttributes() ? $this->buildAttributes($object->getAttributes()) : '';

            if ($url = $object->getUrl())
            {
                $url = $this->getTemplate()->getView()->getActivityRoute($url);
                $output = "<a {$attribs} href=\"{$url}\">{$output}</a>";

            } else $output = "<span {$attribs}>{$output}</span>";
        }

        return $output;
    }
}

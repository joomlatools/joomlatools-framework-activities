<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Html Activity Translator Parameter Renderer
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityTranslatorParameterRendererHtml extends ComActivitiesActivityTranslatorParameterRendererAbstract
{
    /**
     * @see ComActivitiesActivityTranslatorParameterRendererInterface::render()
     */
    public function render(ComActivitiesActivityTranslatorParameterInterface $parameter)
    {
        if ($output = $parameter->getText())
        {
            if ($parameter->isTranslatable()) {
                $output = $parameter->getTranslator()->translate($output);
            }

            $output = '<span class="text">' . $output . '</span>';

            if ($parameter->isLinkable())
            {
                $url             = htmlspecialchars($parameter->getUrl(), ENT_QUOTES);
                $link_attributes = $parameter->getLinkAttributes();

                $output = '<a ' . (empty($link_attributes) ? '' : $this->buildAttributes($link_attributes)) . ' href="' . $url . '">' . $output . '</a>';
            }

            $attribs = $parameter->getAttributes();

            if (count($attribs))
            {
                foreach ($attribs as $attrib => $value)
                {
                    if (is_array($value)) {
                        $attribs[$attrib] = implode(' ', $value);
                    }
                }

                $output = '<span ' . $this->buildAttributes($attribs) . '>' . $output . '</span>';
            }
        }
        else $output = $parameter->getLabel();

        return $output;
    }

    /**
     * Method to build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array)
    {
        $output = array();

        if ($array instanceof KObjectConfig) {
            $array = KObjectConfig::unbox($array);
        }

        if (is_array($array))
        {
            foreach ($array as $key => $item)
            {
                if (is_array($item)) {
                    $item = implode(' ', $item);
                }

                $output[] = $key . '="' . str_replace('"', '&quot;', $item) . '"';
            }
        }

        return implode(' ', $output);
    }
}
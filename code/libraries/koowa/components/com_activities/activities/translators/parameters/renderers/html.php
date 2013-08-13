<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Html Activity Parameter Translator Renderer
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
            if ($parameter->isTranslatable())
            {
                $output = $parameter->getTranslator()->translate($output);
            }

            $output = '<span class="content">' . $output . '</span>';

            if ($parameter->isLinkable())
            {
                $url             = htmlspecialchars($parameter->getUrl(), ENT_QUOTES);
                $link_attributes = $parameter->getLinkAttributes();

                $output = '<a ' . (empty($link_attributes) ? '' : KHelperArray::toString($link_attributes)) . ' href="' . $url . '">' . $output . '</a>';
            }

            $attribs = $parameter->getAttributes();

            if (count($attribs))
            {
                // TODO Check FW KHelperArray::toString. It seems that it is not properly working. Need to do the
                // following for having properly rendered attribs.
                foreach ($attribs as $attrib => $value)
                {
                    if (is_array($value))
                    {
                        $attribs[$attrib] = implode(' ', $value);
                    }
                }

                $output = '<span ' . KHelperArray::toString($attribs) . '>' . $output . '</span>';
            }
        }
        else
        {
            $output = $parameter->getLabel();
        }

        return $output;
    }
}
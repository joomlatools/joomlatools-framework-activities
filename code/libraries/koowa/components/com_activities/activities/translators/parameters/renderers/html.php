<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
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

            if (($link = $parameter->getLink()) && $link->getUrl())
            {
                $output = $link->toString($output);
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
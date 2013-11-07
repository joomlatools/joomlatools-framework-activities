<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Text Activity Translator Parameter Renderer
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesTranslatorParameterRendererText extends ComActivitiesTranslatorParameterRendererAbstract
{
    /**
     * @see ComActivitiesTranslatorParameterRendererInterface::render()
     */
    public function render(ComActivitiesTranslatorParameterInterface $parameter)
    {
        $output = $parameter->getText();

        if ($parameter->isTranslatable())
        {
            $output = $parameter->getTranslator()->translate($output);
        }

        return $output;
    }
}
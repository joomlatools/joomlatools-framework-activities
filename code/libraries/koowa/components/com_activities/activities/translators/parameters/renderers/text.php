<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComActivitiesActivityTranslatorParameterRendererText extends ComActivitiesActivityTranslatorParameterRendererAbstract
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
        }
        else
        {
            $output = $parameter->getLabel();
        }

        return $output;
    }
}
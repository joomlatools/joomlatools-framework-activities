<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
interface ComActivitiesActivityTranslatorParameterRendererInterface
{
    /**
     * Renders a parameter object.
     *
     * @param $parameter ComActivitiesActivityTranslatorParameterInterface The parameter object.
     *
     * @return string The rendered parameter object.
     */
    public function render(ComActivitiesActivityTranslatorParameterInterface $parameter);
}
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Abstract Activity Parameter Translator Renderer
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
abstract class ComActivitiesActivityTranslatorParameterRendererAbstract extends KObject implements ComActivitiesActivityTranslatorParameterRendererInterface, KServiceInstantiatable
{
    public static function getInstance(KObjectConfigInterface $config, KServiceInterface $container)
    {
        // Singleton behavior.
        $classname = $config->service_identifier->classname;
        $instance  = new $classname($config);
        $container->set($config->service_identifier, $instance);

        return $instance;
    }
}

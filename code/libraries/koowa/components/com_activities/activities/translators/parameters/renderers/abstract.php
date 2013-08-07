<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
abstract class ComActivitiesActivityTranslatorParameterRendererAbstract extends KObject implements ComActivitiesActivityTranslatorParameterRendererInterface, KServiceInstantiatable
{
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Singleton behavior.
        $classname = $config->service_identifier->classname;
        $instance  = new $classname($config);
        $container->set($config->service_identifier, $instance);

        return $instance;
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Resource Controller.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerResource extends KControllerModel
{
    protected function _initialize(KObjectConfig $config)
    {
        if ($this->getIdentifier()->getPackage() != 'activities')
        {
            $aliases = array(
                'com:activities.model.resources'                => array(
                    'path' => array('model'),
                    'name' => KStringInflector::pluralize($this->getIdentifier()->getName())
                ),
                'com:activities.controller.permission.resource' => array('path' => array('controller', 'permission'))
            );

            foreach ($aliases as $identifier => $alias)
            {
                $alias = array_merge($this->getIdentifier()->toArray(), $alias);

                $manager = $this->getObject('manager');

                // Register the alias if a class for it cannot be found.
                if (!$manager->getClass($alias, false)) {
                    $manager->registerAlias($identifier, $alias);
                }
            }
        }

        parent::_initialize($config);
    }
}
<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Resource Model Entity.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesModelEntityResource extends KModelEntityRow implements ComActivitiesActivityResourceInterface
{
    /**
     * Resource package getter.
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Resource name getter.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Resource ID getter.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Resource UUID getter.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}
<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Activity Resource Interface.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */

interface ComActivitiesActivityResourceInterface extends KDatabaseRowInterface
{
    /**
     * Resource package getter.
     *
     * @return string
     */
    public function getPackage();

    /**
     * Resource name getter.
     *
     * @return string
     */
    public function getName();

    /**
     * Resource ID getter.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Resource UUID getter.
     *
     * @return string
     */
    public function getUuid();
}
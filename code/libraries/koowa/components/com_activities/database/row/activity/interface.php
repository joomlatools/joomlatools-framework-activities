<?php
/**
 * @package        Nooku_Components
 * @subpackage     Activities
 * @copyright      Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */

/**
 * Activity Database Row Interface
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesDatabaseRowActivityInterface
{
    /**
     * Strategy getter.
     *
     * @return ComActivitiesDatabaseRowActivityStrategyInterface|null The activity strategy, null if one cannot be provided.
     */
    public function getStrategy();
}
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
     * Casts an activity row to a string.
     *
     * This string correspond to the message of the activity that the row represents.
     *
     * @param bool $html Whether the HTML (true) or plain text (false) version is returned.
     * @return string The activity message string.
     */
    public function toString($html = true);

    /**
     * Activity stream data getter.
     *
     * @return array Associative array containing formatted activity stream data for the activity row.
     */
    public function getStreamData();
}
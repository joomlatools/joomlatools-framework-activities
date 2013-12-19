<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * View Routable Behavior.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesViewBehaviorRoutable extends KBehaviorAbstract
{
    /**
     * Activity Route Getter.
     *
     * Used for routing URLs from activity parameters.
     *
     * @param string|array $route  The query string used to create the route
     * @param boolean      $escape If TRUE escapes the route for xml compliance. Default FALSE.
     *
     * @return KHttpUrl     The route
     */
    public function getActivityRoute($route = '', $escape = true)
    {
        // Same as getRoute.
        return $this->getMixer()->getRoute($route, true, $escape);
    }
}
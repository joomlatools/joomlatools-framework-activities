<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesDispatcher extends ComKoowaDispatcher
{
	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'request' => array(
				'view' => 'activities'
			),
		));

		parent::_initialize($config);
	}
}

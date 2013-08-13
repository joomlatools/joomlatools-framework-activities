<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Html View
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesViewActivitiesHtml extends ComKoowaViewHtml
{
	public function display()
	{
		if ($this->getLayout() == 'default')
		{
			$model = $this->getService($this->getModel()->getIdentifier());

			$this->assign('packages', $model
				->distinct(true)
				->column('package')
				->getList()
			);
		}

		return parent::display();
	}
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesTemplateHelperActivity extends KTemplateHelperDefault implements KObjectInstantiatable
{
	/**
     * Check for overrides of the helper
     *
     * @param   KObjectConfigInterface $config Configuration options
     * @param 	KObjectManagerInterface $manager Object manager
     * @return ComActivitiesTemplateHelperActivity
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        $identifier = clone $config->object_identifier;
        $identifier->package = $config->row->package;

        $identifier = $manager->getIdentifier($identifier);

        if(file_exists($identifier->filepath)) {
            $classname = $identifier->classname;
        } else {
            $classname = $config->object_identifier->classname;
        }

        $instance  = new $classname($config);
        return $instance;
    }

    public function message($config = array())
	{
	    $config = new KObjectConfig($config);
		$config->append(array(
			'row'      => ''
		));

		$row  = $config->row;

		$item = $this->getTemplate()->getView()->createRoute('option='.$row->type.'_'.$row->package.'&view='.$row->name.'&id='.$row->row);
		$user = $this->getTemplate()->getView()->createRoute('option=com_users&view=user&id='.$row->created_by);

		$message   = '<a href="'.$user.'">'.$row->created_by_name.'</a>';
		$message  .= ' <span class="action">'.$row->status.'</span>';

		if ($row->status != 'deleted') {
			$message .= ' <a href="'.$item.'">'.$row->title.'</a>';
		} else {
			$message .= ' <span class="ellipsis" class="deleted">'.$row->title.'</span>';
		}

		$message .= ' <span class="ellipsis" class="package">'.$row->name.'</span>';

		return $message;
	}
}

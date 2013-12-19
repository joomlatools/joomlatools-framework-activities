<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Json View
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 * @see     http://activitystrea.ms/specs/json/1.0/
 */
class ComActivitiesViewActivitiesJson extends KViewJson
{
    protected $_layout;

    public function __construct(KObjectConfig $config)
    {
        $this->_layout = $config->layout;
        parent::__construct($config);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('behaviors' => array('routable')));
        parent::_initialize($config);
    }

    protected function _getItem(KDatabaseRowInterface $row)
    {
        $data = parent::_getItem($row);

        unset($data['links']); // Cleanup.

        return $data;
    }

    protected function _getActivity(KDatabaseRowInterface $row)
    {
        if (($this->_layout == 'stream') && ($strategy = $row->getStrategy()))
        {
            $message = $strategy->getMessage();

            foreach ($message->getParameters() as $parameter)
            {
                $parameter->setContent($parameter->getText());
            }

            $item = array(
                'id'        => $row->uuid,
                'title'     => $message->toString(),
                'published' => $this->getObject('com://admin/koowa.template.helper.date')->format(array(
                        'date'   => $row->created_on,
                        'format' => 'c'
                    )),
                'verb'      => $this->action,
                'object'    => array(
                    'id'         => $row->row,
                    'objectType' => $row->name),
                'actor'     => array(
                    'id'          => $row->created_by,
                    'objectType'  => 'user',
                    'displayName' => $row->created_by_name));

            if ($strategy->objectExists()) {
                $item['object']['url'] = $this->getActivityRoute($strategy->getObjectUrl(), false);
            }

            if ($strategy->actorExists()) {
                $item['actor']['url'] = $this->getActivityRoute($strategy->getActorUrl(), false);
            }

            if ($strategy->getObjectType() == 'image')
            {
                $item['object']['objectType'] = 'image';

                // Append media info.
                if ($strategy->objectExists())
                {
                    $item['object']['image'] = array(
                        'url' => $this->getActivityRoute($strategy->getObjectUrl(), false)
                    );

                    if ($row->metadata->width && $row->metadata->height)
                    {
                        $item['object']['image']['width']  = $row->metadata->width;
                        $item['object']['image']['height'] = $row->metadata->height;
                    }
                }
            }

            if ($strategy->hasTarget())
            {
                $item['target'] = array(
                    'id'         => $strategy->getTargetId(),
                    'objectType' => $strategy->getTargetType()
                );

                if ($strategy->targetExists())
                {
                    $item['target']['url'] = $this->getActivityRoute($strategy->getTargetUrl(), false);
                }
            }
        } else {
            $item = $row->toArray();
        }

        return $item;
    }
}

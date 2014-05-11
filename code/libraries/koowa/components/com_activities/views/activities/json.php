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

    protected function _getEntity(KModelEntityInterface $entity)
    {
        $data = parent::_getEntity($entity);

        unset($data['links']); // Cleanup.

        return $data;
    }

    protected function _getActivity(KModelEntityInterface $entity)
    {
        if ($this->_layout == 'stream')
        {
            $item = array(
                'id'        => $entity->uuid,
                'title'     => $entity->toString(),
                'published' => $this->getObject('com://admin/koowa.template.helper.date')->format(array(
                        'date'   => $entity->created_on,
                        'format' => 'c'
                    )),
                'verb'      => $entity->action,
                'object'    => array(
                    'id'         => $entity->row,
                    'objectType' => $entity->name),
                'actor'     => array(
                    'id'          => $entity->created_by,
                    'objectType'  => 'user',
                    'displayName' => $entity->created_by_name));

            if ($entity->hasObject()) {
                $item['object']['url'] = $this->getActivityRoute($entity->getObjectUrl(), false);
            }

            if ($entity->hasActor()) {
                $item['actor']['url'] = $this->getActivityRoute($entity->getActorUrl(), false);
            }

            $object_type = $entity->getObjectType();
            $item['object']['objectType'] = $object_type;

            if (in_array($object_type, array('image', 'photo', 'photograph', 'picture', 'icon')))
            {
                // Append media info.
                if ($entity->hasObject())
                {
                    $item['object']['image'] = array(
                        'url' => $this->getActivityRoute($entity->getObjectUrl(), false)
                    );

                    if ($entity->metadata->width && $entity->metadata->height)
                    {
                        $item['object']['image']['width']  = $entity->metadata->width;
                        $item['object']['image']['height'] = $entity->metadata->height;
                    }
                }
            }

            if ($entity->hasTarget())
            {
                $item['target'] = array(
                    'id'         => $entity->getTargetId(),
                    'objectType' => $entity->getTargetType(),
                    'url'        => $this->getActivityRoute($entity->getTargetUrl(), false)
                );
            }
        }
        else $item = $entity->toArray();

        return $item;
    }
}

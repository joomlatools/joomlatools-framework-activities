<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
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
        $config->append(array(
            'behaviors' => array('routable')
        ));

        parent::_initialize($config);
    }

    protected function _getEntity(KModelEntityInterface $entity)
    {
        return $this->_getActivity($entity);
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
                'verb'      => $entity->action
            );

            foreach ($entity->getStreamObjects() as $name => $object)
            {
                $item[$name] = $this->_getStreamObjectData($object);
            }
        } else {
            $item = $entity->toArray();
            if (!empty($this->_fields)) {
                $item = array_intersect_key($item, array_flip($this->_fields));
            }
        }

        return $item;
    }

    /**
     * Activity stream object data getter.
     *
     * @param ComActivitiesActivityStreamObjectInterface $object The stream object.
     *
     * @return array The data.
     */
    protected function _getStreamObjectData(ComActivitiesActivityStreamObjectInterface $object)
    {
        $data = $object->toArray();

        // Route object URL.
        if ($url = $object->getUrl()) {
            $data['url'] = $this->getActivityRoute($url, false);
        }

        // Route image URL if any.
        if (($image = $object->getImage()) && ($url = $image->getUrl())) {
            $data['image']['url'] = $this->getActivityRoute($url, false);
        }

        $attachments = array();

        // Process attachments if any.
        foreach ($object->getAttachments() as $attachment) {
            $attachments[] = $this->_getStreamObjectData($attachment);
        }

        $data['attachments'] = $attachments;

        // Remove properties with empty arrays.
        foreach ($data as $name => $value) {
            if (is_array($value) && empty($value)) unset($data[$name]);
        }

        return $data;
    }
}

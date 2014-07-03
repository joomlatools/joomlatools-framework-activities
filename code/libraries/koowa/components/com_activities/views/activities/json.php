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

    /**
     * Template helper for rendering activities.
     *
     * @var mixed
     */
    protected $_helper;

    public function __construct(KObjectConfig $config)
    {
        $this->_layout = $config->layout;

        parent::__construct($config);

        $this->_helper = $config->helper;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'helper'    => 'activity',
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
            $activity   = $entity;
            $format = $activity->getFormat();

            // Set the activity and story
            $activity->setTitle($this->_renderTitle($activity));
            $activity->setStory($this->_renderStory($activity));

            $item = array(
                'id'        => $activity->getId(),
                'title'     => $activity->getTitle(),
                'content'   => $activity->getContent(),
                'story'     => $activity->getStory(),
                'published' => $activity->getPublished()->format('c'),
                'verb'      => $activity->getVerb(),
                'format'    => array(
                    'string'     => $format->getString(),
                    'parameters' => $this->_getFormatParametersData($format->getParameters()))
            );

            if ($activity->getIcon()) {
                $item['icon'] = $this->_getMediaLinkData($activity->getIcon());
            }

            foreach ($activity->getObjects() as $name => $object)
            {
                $item[$name] = $this->_getObjectData($object);
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
     * Template helper getter.
     *
     * @return KTemplateHelperInterface The template helper.
     * @throws UnexpectedValueException
     */
    public function getHelper()
    {
        if (!$this->_helper instanceof KTemplateHelperInterface)
        {
            //Make sure we have a model identifier
            if(!($this->_helper instanceof KObjectIdentifier)) {
                $this->setHelper($this->_helper);
            }

            $this->_helper = $this->getObject($this->_helper);

            if(!$this->_helper instanceof KTemplateHelperInterface)
            {
                throw new UnexpectedValueException(
                    'Helper: '.get_class($this->_model).' does not implement KTemplateHelperInterface'
                );
            }

            // Push view to the template for accessing view mixed methods within the template helper.
            $this->_helper->getTemplate()->setView($this);
        }

        return $this->_helper;
    }

    /**
     * Template helper setter.
     *
     * @param mixed $helper A template helper instance, identifier object or string.
     *
     * @return $this
     */
    public function setHelper($helper)
    {
        if(!$helper instanceof KTemplateHelperInterface)
        {
            if(is_string($helper) && strpos($helper, '.') === false )
            {
                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('template', 'helper');
                $identifier['name']	= $helper;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($helper);

            $helper = $identifier;
        }

        $this->_helper = $helper;

        return $this;
    }

    /**
     * Renders the activity title.
     *
     * @param ComActivitiesModelEntityActivity $activity The activity object.
     *
     * @return string The activity title.
     */
    protected function _renderTitle(ComActivitiesModelEntityActivity $activity)
    {
        return $this->getHelper()->render(array('entity' => $activity, 'html' => true));
    }

    /**
     * Renders the activity story.
     *
     * @param ComActivitiesModelEntityActivity $activity The activity object.
     *
     * @return string The activity story.
     */
    protected function _renderStory(ComActivitiesModelEntityActivity $activity)
    {
        return $this->getHelper()->render(array('entity' => $activity, 'html' => false));
    }

    /**
     * Activity object data getter.
     *
     * @param ComActivitiesActivityObjectInterface $object The object.
     *
     * @return array The data.
     */
    protected function _getObjectData(ComActivitiesActivityObjectInterface $object)
    {
        $data = $object->toArray();

        // Route object URL.
        if ($url = $object->getUrl()) {
            $data['url'] = $this->getActivityRoute($url, false);
        }

        // Route image URL if any.
        if ($image = $object->getImage()) {
            $data['image'] = $this->_getMediaLinkData($image);
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

    /**
     * Activity object data getter.
     *
     * @param ComActivitiesActivityObjectInterface $object The object.
     *
     * @return array The data.
     */
    protected function _getMediaLinkData(ComActivitiesActivityMedialinkInterface $medialink)
    {
        $data = $medialink->toArray();

        // Route medialink URL.
        if ($url = $medialink->getUrl()) {
            $data['url'] = $this->getActivityRoute($url, false);
        }

        return $data;
    }

    protected function _getFormatParametersData(array $parameters = array())
    {
        $data = array();

        foreach ($parameters as $parameter) {
            $data[$parameter->getName()] = $this->_getFormatParameterData($parameter);
        }

        return $data;
    }

    protected function _getFormatParameterData(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $data = $parameter->toArray();

        // Route link URL if any.
        if ($parameter->isLinkable()) {
            $data['link']['href'] = $this->getActivityRoute($parameter->getLink()->href, false);
        }

        // Cleanup.
        if (empty($data['link']['attribs'])) {
            unset($data['link']['attribs']);
        }

        if (empty($data['link'])) {
            unset($data['link']);
        }

        return $data;
    }
}

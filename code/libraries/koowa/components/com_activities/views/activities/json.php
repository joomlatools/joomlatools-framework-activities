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
     * Activities renderer.
     *
     * @var mixed
     */
    protected $_renderer;

    public function __construct(KObjectConfig $config)
    {
        $this->_layout = $config->layout;

        parent::__construct($config);

        $this->_renderer = $config->renderer;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('renderer'  => 'activity'));

        parent::_initialize($config);
    }

    protected function _getEntity(KModelEntityInterface $entity)
    {
        if ($this->_layout == 'stream')
        {
            $activity = $entity;
            $renderer = $this->getRenderer();

            $item = array(
                'id'        => $activity->getActivityId(),
                'title'     => $renderer->render($activity),
                'story'     => $renderer->render($activity, array('html' => false)),
                'published' => $activity->getActivityPublished()->format('c'),
                'verb'      => $activity->getActivityVerb(),
                'format'    => $activity->getActivityFormat()
            );

            if ($icon = $activity->getActivityIcon()) {
                $item['icon'] = $this->_getMediaLinkData($icon);
            }

            foreach ($activity->objects as $name => $object)
            {

                $item[$name] = $this->_getObjectData($object);
            }
        }
        else
        {
            $item = $entity->toArray();
            if (!empty($this->_fields)) {
                $item = array_intersect_key($item, array_flip($this->_fields));
            }
        }

        return $item;
    }

    /**
     * Activity renderer getter.
     *
     * @return KTemplateHelperInterface The activity renderer.
     * @throws UnexpectedValueException
     */
    public function getRenderer()
    {
        if (!$this->_renderer instanceof KTemplateHelperInterface)
        {
            // Make sure we have an identifier
            if(!($this->_renderer instanceof KObjectIdentifier)) {
                $this->setRenderer($this->_renderer);
            }

            $this->_renderer = $this->getObject($this->_renderer);

            if(!$this->_renderer instanceof ComActivitiesActivityRendererInterface)
            {
                throw new UnexpectedValueException(
                    'Renderer: '.get_class($this->_renderer).' does not implement ComActivitiesActivityRendererInterface'
                );
            }
        }

        return $this->_renderer;
    }

    /**
     * Activity renderer setter.
     *
     * @param mixed $renderer An activity renderer instance, identifier object or string.
     *
     * @return $this
     */
    public function setRenderer($renderer)
    {
        if(!$renderer instanceof ComActivitiesActivityRendererInterface)
        {
            if(is_string($renderer) && strpos($renderer, '.') === false )
            {
                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('template', 'helper');
                $identifier['name']	= $renderer;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($renderer);

            $renderer = $identifier;
        }

        $this->_renderer = $renderer;

        return $this;
    }

    protected function _getObjectData(ComActivitiesActivityObjectInterface $object)
    {
        $data = $object->toArray();

        // Make sure we get fully qualified URLs.
        if ($url = $object->getUrl()) {
            $data['url'] = $this->_getUrl($url);
        }

        $attachments = array();

        // Handle attachments recursively.
        foreach ($object->getAttachments() as $attachment) {
            $attachments[] = $this->_getObjectData($attachment);
        }

        $data['attachments'] = $attachments;

        // Convert date objects to date time strings.
        foreach (array('published', 'updated') as $property)
        {
            $method = 'get' . ucfirst($property);

            if ($date = $object->$method()) {
                $data[$property] = $date->format('M d Y H:i:s');
            }
        }

        return $this->_cleanupData($data);
    }

    protected function _getMedialinkData(ComActivitiesActivityMedialinkInterface $medialink)
    {
        $data = $medialink->toArray();

        $data['url'] = $this->_getUrl($medialink->getUrl());

        return $this->_cleanupData($data);
    }

    protected function _cleanupData(array $data = array())
    {
        $clean = array();

        foreach ($data as $key => $value)
        {
            if (!empty($value)) {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }

    /**
     * URL getter.
     *
     * @param KHttpUrl $url The URL.
     *
     * @return string The fully qualified un-escaped URL.
     */
    protected function _getUrl(KHttpUrl $url)
    {
        $parts = array();

        $url = clone $url;

        // Do not escape URLs.
        $url->escape = false;

        $site_url = $this->getUrl();

        foreach (array('scheme', 'host', 'port') as $part)
        {
            $method = 'get' . ucfirst($part);

            if (!$url->$method() && ($value = $site_url->$method())) {
                $parts[$part] = $value;
            }
        }

        if (!empty($parts)) {
            $url->setUrl($parts);
        }

        return $url->toString();
    }
}
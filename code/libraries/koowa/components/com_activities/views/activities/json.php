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
                $item['icon'] = $this->_getActivityMediaLinkData($icon);
            }

            foreach ($activity->objects as $name => $object)
            {
                $item[$name] = $this->_getActivityObjectData($object);
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

            // Push view to the template for accessing view mixed methods within the template helper.
            $this->_renderer->getTemplate()->setView($this);
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

    /**
     * Activity object data getter.
     *
     * @param ComActivitiesActivityObjectInterface $object The object.
     *
     * @return array The data.
     */
    protected function _getActivityObjectData(ComActivitiesActivityObjectInterface $object)
    {
        $data = $object->toArray();

        // Route object URL.
        if ($url = $object->getUrl()) {
            $data['url'] = $this->getActivityRoute($url, false);
        }

        // Route image URL if any.
        if ($image = $object->getImage()) {
            $data['image'] = $this->_getActivityMediaLinkData($image);
        }

        $attachments = array();

        // Process attachments if any.
        foreach ($object->getAttachments() as $attachment) {
            $attachments[] = $this->_getActivityObjectData($attachment);
        }

        $data['attachments'] = $attachments;

        // Remove properties with empty arrays.
        foreach ($data as $name => $value) {
            if (is_array($value) && empty($value)) unset($data[$name]);
        }

        // Remove deleted property if the object isn't deleted or deletable.
        if (!$object->isDeleted()) {
            unset($data['deleted']);
        }

        // Route link URL if any.
        if (($link = $object->getLink()) && isset($link['href'])) {
            $data['link']['href'] = $this->getActivityRoute($link['href'], false);
        }

        // Translate value if any and if needed.
        if ($object->isTranslatable() && $object->getValue()) {
            $data['value'] = $this->getObject('translator')->translate($object->getValue());
        }

        // Remove translatable status.
        unset($data['translate']);

        return $data;
    }

    /**
     * Activity object data getter.
     *
     * @param ComActivitiesActivityObjectInterface $object The object.
     *
     * @return array The data.
     */
    protected function _getActivityMediaLinkData(ComActivitiesActivityMedialinkInterface $medialink)
    {
        $data = $medialink->toArray();

        // Route medialink URL.
        if ($url = $medialink->getUrl()) {
            $data['url'] = $this->getActivityRoute($url, false);
        }

        return $data;
    }
}

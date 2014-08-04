<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Stream Object
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityObject extends KObjectConfigJson implements ComActivitiesActivityObjectInterface
{
    /**
     * The activity object label, e.g. (actor, object, target, ...).
     *
     * @var string
     */
    private $__label;

    public function __construct($label, $config = array())
    {
        parent::__construct($config);

        $this->append(array(
            'deleted'              => false,
            'internal'             => false,
            'attachments'          => array(),
            'downstreamDuplicates' => array(),
            'upstreamDuplicates'   => array(),
            'attributes'           => array()
        ));

        $this->setLabel($label);
    }

    public function setLabel($label)
    {
        $this->__label = (string) $label;
        return $this;
    }

    public function getLabel()
    {
        return $this->__label;
    }

    public function setAttachments(array $attachments, $merge = true)
    {
        if ($merge) {
            $this->attachments->append($attachments);
        } else {
            $this->attachments = $attachments;
        }

        return $this;
    }

    public function getAttachments()
    {
        return $this->attachments->toArray();
    }

    public function setAuthor($author)
    {
        if (!is_null($author) && !$author instanceof ComActivitiesActivityObjectInterface) {
            throw new InvalidArgumentException('Invalid author type.');
        }

        $this->author = $author;
        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setContent($content)
    {
        if (!is_null($content)) {
            $content = (string) $content;
        }

        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setObjectName($name)
    {
        if (!is_null($name)) {
            $name = (string) $name;
        }

        $this->objectName = $name;
        return $this;
    }

    public function getObjectName()
    {
        return $this->objectName;
    }

    public function setDisplayName($name)
    {
        if (!is_null($name)) {
            $name = (string) $name;
        }

        $this->displayName = $name;
        return $this;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDownstreamDuplicates(array $duplicates, $merge = true)
    {
        if ($merge) {
            $this->downstreamDuplicates->append($duplicates);
        } else {
            $this->downstreamDuplicates = $duplicates;
        }

        return $this;
    }

    public function getDownstreamDuplicates()
    {
        return $this->downstreamDuplicates;
    }

    public function setId($id)
    {
        if (!is_null($id)) {
            $id = (string) $id;
        }

        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setImage($image)
    {
        if (!is_null($image) && !$image instanceof ComActivitiesActivityMedialinkInterface) {
            throw new InvalidArgumentException('Invalid image type.');
        }

        $this->image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setObjectType($type)
    {
        if (!is_null($type)) {
            $type = (string) $type;
        }

        $this->objectType = $type;
        return $this;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function setPublished($date)
    {
        if (!is_null($date) && !$date instanceof KDate) {
            throw new InvalidArgumentException('Invalid date type.');
        }

        $this->published = $date;
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setSummary($summary)
    {
        if (!is_null($summary)) {
            $summary = (string) $summary;
        }

        $this->summary = $summary;
        return $this;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setUpdated($date)
    {
        if (!is_null($date) && !$date instanceof KDate) {
            throw new InvalidArgumentException('Invalid date type.');
        }

        $this->updated = $date;
        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpstreamDuplicates(array $duplicates, $merge = true)
    {
        if ($merge) {
            $this->downstreamDuplicates->append($duplicates);
        } else {
            $this->upstreamDuplicates = $duplicates;
        }

        return $this;
    }

    public function getUpstreamDuplicates()
    {
        return $this->upstreamDuplicates;
    }

    public function setUrl($url)
    {
        if (!is_null($url) && !$url instanceof KHttpUrl) {
            throw new InvalidArgumentException('Invalid url type.');
        }

        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setDeleted($state)
    {
        $this->deleted = (bool) $state;
        return $this;
    }

    public function isDeleted()
    {
        return $this->deleted;
    }

    public function setAttributes(array $attribs = array(), $merge = true)
    {
        if ($merge) {
            $this->attributes->append($attribs);
        } else {
            $this->attributes = $attribs;
        }

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes->toArray();
    }

    /**
     * Set a parameter property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $value = new KObjectConfigJson($value);
        }

        parent::set($name, $value);

        return $this;
    }

    public function setInternal($state)
    {
        $this->internal = (bool) $state;
        return $this;
    }

    public function isInternal()
    {
        return $this->internal;
    }
}
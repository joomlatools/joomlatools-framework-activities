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
     * The activity object name, e.g. (actor, object, target, ...).
     *
     * @var string
     */
    private $__name;

    /**
     * An array containing allowed settable properties.
     *
     * @var array
     */
    protected $_allowed;

    public function __construct($name, $config = array())
    {
        parent::__construct($config);

        $this->append(array(
            'parameter'            => false,
            'translate'            => true,
            'deleted'              => false,
            'attachments'          => array(),
            'downstreamDuplicates' => array(),
            'upstreamDuplicates'   => array(),
            'attributes'           => array(),
            'link'                 => array()
        ));

        $this->_allowed = array(
            'parameter',
            'translate',
            'deleted',
            'attachments',
            'downstreamDuplicates',
            'upstreamDuplicates',
            'attributes',
            'link',
            'author',
            'content',
            'displayName',
            'id',
            'image',
            'type',
            'published',
            'summary',
            'updated',
            'url',
            'value',
        );

        $this->setName($name);
    }

    public function setName($name)
    {
        $this->__name = (string) $name;
        return $this;
    }

    public function getName()
    {
        return $this->__name;
    }

    public function setValue($value)
    {
        if (!is_null($value)) {
            $value = (string) $value;
        }

        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
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
        if (!is_null($url)) {
            $url = (string) $url;
        }

        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setDeleted($status)
    {
        $this->deleted = (bool) $status;
        return $this;
    }

    public function isDeleted()
    {
        return $this->deleted;
    }

    public function setLink(array $attribs = array(), $merge = true)
    {
        if ($merge) {
            $this->link->append($attribs);
        } else {
            $this->link = $attribs;
        }

        return $this;
    }

    public function getLink()
    {
        return $this->link;
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

    public function isTranslatable()
    {
        return $this->translate;
    }

    public function translate($status = true)
    {
        $this->translate = (bool) $status;
        return $this;
    }

    public function isParameter()
    {
        return $this->parameter;
    }

    public function parameter($status = true)
    {
        $this->parameter = (bool) $status;
        return $this;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $properties = array('published', 'updated');

        // Convert date objects to date time strings.
        foreach ($properties as $property) {
            if (isset($data[$property])) {
                $date = $data[$property];
                $data[$property] = $date->format('M d Y H:i:s');
            }
        }

        $data = $this->_cleanup($data);

        return $data;
    }

    /**
     * Removes empty entries.
     *
     * @param array $data The data to be cleaned up.
     *
     * @return array Cleaned up data.
     */
    protected function _cleanup(array $data = array())
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($data), RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $key => $value) {
            if (empty($value)) {
                $iterator->offsetUnset($key);
            }
        }

        return $iterator->getArrayCopy();
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
        if (in_array($name, $this->_allowed))
        {
            if (is_array($value)) {
                $value = new KObjectConfigJson($value);
            }

            parent::set($name, $value);
        }

        return $this;
    }
}
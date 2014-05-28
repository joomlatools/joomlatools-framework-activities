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
class ComActivitiesActivityStreamObject extends KObjectConfigJson implements ComActivitiesActivityStreamObjectInterface
{
    /**
     * The activity object name, e.g. (actor, object, target, ...).
     *
     * @var string
     */
    private $__name;

    /**
     * Object deleted status.
     *
     * @var bool
     */
    private $__deleted;

    public function __construct($name, $config = array())
    {
        parent::__construct($config);

        $this->append(array(
            'attachments'          => array(),
            'downstreamDuplicates' => array(),
            'upstreamDuplicates'   => array(),
        ));

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
        return $this->attachments;
    }

    public function setAuthor(ComActivitiesActivityObjectInterface $author)
    {
        $this->author = $author;
        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setContent($content)
    {
        $this->content = (string) $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setDisplayName($name)
    {
        $this->displayName = (string) $name;
        return $this;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDownstreamDuplicates(array $duplicates, $merge = true)
    {
        if ($merge) {
            $this->append(array('downstreamDuplicates' => $duplicates));
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
        $this->id = (string) $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setImage(ComActivitiesActivityStreamMedialinkInterface $image)
    {
        $this->image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setObjectType($type)
    {
        $this->objectType = (string) $type;
        return $this;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function setPublished(KDate $date)
    {
        $this->published = $date;
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setSummary($summary)
    {
        $this->summary = (string) $summary;
        return $this;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setUpdated(KDate $date)
    {
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
            $this->append(array('downstreamDuplicates' => $duplicates));
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
        $this->url = (string) $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setDeleted($status)
    {
        $this->__deleted = (bool) $status;
        return $this;
    }

    public function isDeleted()
    {
        return $this->__deleted;
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

        return $data;
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
    }
}

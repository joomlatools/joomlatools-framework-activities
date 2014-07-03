<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesModelEntityActivity extends KModelEntityRow implements KObjectInstantiable, ComActivitiesActivityInterface
{
    /**
     * The activity format.
     *
     * @var mixed
     */
    protected $_format;

    /**
     * The activity content.
     *
     * @var string|null
     */
    protected $_content;

    /**
     * The activity story.
     *
     * @var string|null;
     */
    protected $_story;

    /**
     * The activity icon.
     *
     * @var ComActivitiesActivityMedialinkInterface|null
     */
    protected $_icon;

    /**
     * The activity published date.
     *
     * @var KDate
     */
    protected $_published;

    /**
     * The activity title.
     *
     * @var string|null
     */
    protected $_title;

    /**
     * Activity object.
     *
     * @var ComActivitiesActivityObjectInterface|null
     */
    protected $_object;

    /**
     * Activity actor.
     *
     * @var ComActivitiesActivityObjectInterface
     */
    protected $_actor;

    /**
     * Activity target.
     *
     * @var ComActivitiesActivityObjectInterface|null
     */
    protected $_target;

    /**
     * Activity generator.
     *
     * @var ComActivitiesActivityObjectInterface|null
     */
    protected $_generator;

    /**
     * Activity provider.
     *
     * @var ComActivitiesActivityObjectInterface|null
     */
    protected $_provider;

    /**
     * A list of required columns.
     *
     * @var array
     */
    protected $_required = array('package', 'name', 'action', 'title', 'status');

    /**
     * The activity object database table name.
     *
     * @var string
     */
    protected $_object_table;

    /**
     * The activity object database table id column.
     *
     * @var string
     */
    protected $_object_column;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_icon          = $config->icon;
        $this->_title         = $config->title;
        $this->_content       = $config->content;
        $this->_story         = $config->story;
        $this->_actor         = $config->actor;
        $this->_object        = $config->object;
        $this->_target        = $config->target;
        $this->_format        = $config->format;
        $this->_object_table  = $config->object_table;
        $this->_object_column = $config->object_column;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $data = $config->data;

        $config->append(array(
            'format'        => '{actor} {action} {object} {title}',
            'object_table'  => $data->package . '_' . KStringInflector::pluralize($data->name),
            'object_column' => $data->package . '_' . $data->name . '_id'
        ));

        parent::_initialize($config);
    }

    /**
     * Instantiates the object
     *
     * @param   KObjectConfigInterface $config      Configuration options
     * @param 	KObjectManagerInterface $manager	A KObjectManagerInterface object
     * @return  KObjectInterface
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if ($config->object_identifier->class == get_class())
        {
            $identifier            = $config->object_identifier->toArray();
            $identifier['package'] = $config->data->package;

            if ($class = $manager->getClass($identifier, false)) {
                return $manager->getObject($identifier, $config->toArray());
            }
        }

        return new $config->object_identifier->class($config);
    }

    public function save()
    {
        // Activities are immutable.
        if (!$this->isNew()) {
            throw new RuntimeException('Activities cannot be modified.');
        }

        if (!$this->status)
        {
            // Attempt to provide a default status.
            switch ($this->verb)
            {
                case 'add':
                    $status = KDatabase::STATUS_CREATED;
                    break;
                case 'edit':
                    $status = KDatabase::STATUS_UPDATED;
                    break;
                case 'delete':
                    $status = KDatabase::STATUS_DELETED;
                    break;
                default:
                    $status = null;
            }

            if ($status) {
                $this->status = $status;
            }
        }

        foreach ($this->_required as $column)
        {
            if (empty($this->$column))
            {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage($this->getObject('translator')->translate('Missing required data'));
                return false;
            }
        }

        return parent::save();
    }

    public function removeProperty($name)
    {
        if ($name == 'package') {
            throw new RuntimeException('Entity package property cannot be removed.');
        }

        return parent::removeProperty($name);
    }

    public function setPropertyPackage($value)
    {
        if ($this->package && $this->package != $value) {
            throw new RuntimeException('Entity package cannot be modified.');
        }

        return $value;
    }

    public function getPropertyVerb()
    {
        return $this->action;
    }

    /**
     * Overridden for resetting activity properties when the entity changes.
     */
    public function setProperty($name, $value, $modified = true)
    {
        parent::setProperty($name, $value, $modified = true);

        // Reset activity properties.
        if ($modified) {
            $this->_resetActivity();
        }

        return $this;
    }

    /**
     * Overridden for resetting activity properties on entity reset.
     */
    public function reset()
    {
        parent::reset();
        $this->_resetActivity();

        return $this;
    }

    /**
     * Resets activity properties.
     *
     * @return $this
     */
    protected function _resetActivity()
    {
        $properties = array(
            '_format',
            '_content',
            '_story',
            '_icon',
            '_published',
            '_title',
            '_object',
            '_actor',
            '_target',
            '_generator',
            '_provider');

        foreach ($properties as $property) {
            $this->{$property} = null;
        }

        return $this;
    }

    public function setFormat(ComActivitiesActivityFormatInterface $format)
    {
        $this->_format = $format;
        return $this;
    }

    public function getFormat()
    {
        if (!$this->_format instanceof ComActivitiesActivityFormatInterface) {

            $format = (string) $this->_format;

            $parameters = array();

            if (preg_match_all('/\{(.*?)\}/', $format, $matches) !== false)
            {
                $translator = $this->getObject('translator');

                foreach ($matches[1] as $name)
                {
                    $method = '_parameter'.ucfirst($name);

                    if (method_exists($this, $method))
                    {
                        $parameter = new ComActivitiesActivityFormatParameter($name, $translator);
                        $this->$method($parameter);

                        $parameters[$parameter->getName()] = $parameter;
                    }
                }
            }

            $translator = $this->getObject('com:activities.activity.translator');

            // Translate format to a readable translated string.
            $string = $translator->translate($translator->getOverride($format, $parameters));

            $format = $this->getObject('com:activities.activity.format',
                array('string' => $string, 'parameters' => $parameters));

            $this->setFormat($format);
        }

        return $this->_format;
    }

    public function setContent($content)
    {
        $this->_content = (string) $content;
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function setStory($story)
    {
        $this->_story = (string) $story;
        return $this;
    }

    public function getStory()
    {
        return $this->_story;
    }

    public function setIcon(ComActivitiesActivityMedialinkInterface $icon)
    {
        $this->_icon = $icon;
        return $this;
    }

    public function getIcon()
    {
        if ($this->_icon && !$this->_icon instanceof ComActivitiesActivityMedialinkInterface) {
            $this->setIcon(new ComActivitiesActivityMedialink(array('url' => (string) $this->_icon)));
        }

        return $this->_icon;
    }

    public function setId($id)
    {
        $this->uuid = (string) $id;
        return $this;
    }

    public function getId()
    {
        return $this->uuid;
    }

    public function setPublished(KDate $date)
    {
        // Sync entity data before setting published property.
        $this->created_on = $date->format('Y-m-d H:i:s');

        $this->_published = $date;

        return $this;
    }

    public function getPublished()
    {
        if (!$this->_published instanceof KDate) {
            $this->setPublished($this->getObject('lib:date', array('date' => $this->created_on)));
        }

        return $this->_published;
    }

    public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setVerb($verb)
    {
        $this->action = (string) $verb;
    }

    public function getVerb()
    {
        return $this->verb;
    }

    /*
     * Activity objects - Begin
     */

    public function setObjectActor(ComActivitiesActivityObjectInterface $actor)
    {
        $this->_actor = $actor;
        return $this;
    }

    public function getObjectActor()
    {
        if (!$this->_actor instanceof ComActivitiesActivityObjectInterface)
        {
            $actor = new ComActivitiesActivityObject('actor');

            $actor->objectType = 'user';
            $actor->id         = $this->created_by;
            $actor->url        = 'option=com_users&task=user.edit&id=' . $this->created_by;

            if (!$this->getObject('user.provider')->load($this->created_by)->getId())
            {
                $actor->setDeleted(true);
            }

            $this->setObjectActor($actor);
        }

        return $this->_actor;
    }

    public function setObjectObject(ComActivitiesActivityObjectInterface $object)
    {
        $this->_object = $object;
        return $this;
    }

    public function getObjectObject()
    {
        if (!$this->_object instanceof ComActivitiesActivityObjectInterface)
        {
            $object = new ComActivitiesActivityObject('object');

            $object->id         = $this->row;
            $object->objectType = $this->name;
            $object->url        = 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row;

            if (!$this->_findObjectObject())
            {
                $object->setDeleted(true);
            }

            $this->setObjectObject($object);
        }

        return $this->_object;
    }

    public function setObjectTarget(ComActivitiesActivityObjectInterface $target)
    {
        $this->_target = $target;
        return $this;
    }

    public function getObjectTarget()
    {
        return $this->_target; // Activities do not have targets by default, return current target value.
    }

    public function setObjectGenerator(ComActivitiesActivityObjectInterface $generator)
    {
        $this->_generator = $generator;
        return $this;
    }

    public function getObjectGenerator()
    {
        if (!$this->_generator instanceof ComActivitiesActivityObjectInterface)
        {
            $generator = new ComActivitiesActivityObject('generator');
            $generator->setDisplayName('com_activities')->setObjectType('component');
            $this->setObjectGenerator($generator);
        }

        return $this->_generator;
    }

    public function setObjectProvider(ComActivitiesActivityObjectInterface $provider)
    {
        $this->_provider = $provider;
        return $this;
    }

    public function getObjectProvider()
    {
        if (!$this->_provider instanceof ComActivitiesActivityObjectInterface)
        {
            $provider = new ComActivitiesActivityObject('provider');
            $$provider->setDisplayName('com_activities')->setObjectType('component');
            $this->setObjectProvider($provider);
        }

        return $this->_provider;
    }

    public function getObjects()
    {
        $objects = array();

        foreach ($this->getMethods() as $method)
        {
            if (strpos($method, 'getObject') === 0 && !in_array($method, array('getObject', 'getObjects')))
            {
                $object = $this->$method();

                if ($object instanceof ComActivitiesActivityObjectInterface) {
                    $name           = strtolower(str_replace('getObject', '', $method));
                    $objects[$name] = $object;
                }
            }
        }

        return $objects;
    }

   /*
    * Activity objects - End
    */

    /*
     * Activity format parameters configurators - Begin
     */

    /**
     * Actor format parameter configurator.
     *
     * @param ComActivitiesActivityFormatParameterInterface $parameter The activity format parameter.
     * @return  void
     */
    protected function _parameterActor(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $actor = $this->getObjectActor();

        if (!$actor->isDeleted())
        {
            $parameter->link->href = $actor->url;
            $parameter->translate  = false;
            $value                 = $this->created_by_name;
        }
        else
        {
            $value = $this->created_by ? 'Deleted user' : 'Guest user';
        }

        $parameter->value = $value;
    }

    /**
     * Action format parameter configurator.
     *
     * @param ComActivitiesActivityFormatParameterInterface $parameter The activity format parameter.
     * @return  void
     */
    protected function _parameterAction(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $parameter->value = $this->status;
    }

    /**
     * Object format parameter configurator.
     *
     * @param ComActivitiesActivityFormatParameterInterface $parameter The activity format parameter.
     * @return  void
     */
    protected function _parameterObject(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $parameter->value = $this->getObjectObject()->getObjectType();

        $parameter->append(array(
            'attribs' => array('class' => array('object')),
        ));
    }

    /**
     * Title format parameter configurator.
     *
     * @param ComActivitiesActivityFormatParameterInterface $parameter The activity format parameter.
     * @return  void
     */
    protected function _parameterTitle(ComActivitiesActivityFormatParameterInterface $parameter)
    {
        $parameter->value     = $this->title;
        $parameter->translate = false;

        $object = $this->getObjectObject();

        if (!$object->isDeleted() && $object->url) {
            $parameter->link->href = $object->url;
        }

        if ($this->status == 'deleted') {
            $parameter->attribs = array('class' => array('deleted'));
        }
    }

   /*
    * Activity format parameters configurators - End
    */

    /**
     * Looks for the activity object object.
     *
     * This method may be overridden for activities persisting objects on storage systems other than local
     * database tables.
     *
     * @return boolean True if found, false otherwise.
     */
    protected function _findObjectObject()
    {
        $db     = $this->getTable()->getAdapter();
        $table  = $this->_object_table;
        $column = $this->_object_column;

        $query = $this->getObject('lib:database.query.select');
        $query->columns('COUNT(*)')->table($table)->where($column . ' = :value')
              ->bind(array('value' => $this->row));

        // Need to catch exceptions here as table may not longer exist.
        try {
            $result = $db->select($query, KDatabase::FETCH_FIELD);
        } catch (Exception $e) {
            $result = 0;
        }

        return (bool) $result;
    }
}

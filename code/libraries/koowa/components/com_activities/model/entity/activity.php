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
     * @var string
     */
    protected $_format;

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
     * An associative list of found activity objects.
     *
     * @var array
     */
    static protected $_found_objects = array();

    /**
     * An associative array containing activity objects.
     *
     * @var array
     */
    protected $_objects = array();

    /**
     * A list of activity object getters.
     *
     * @var array
     */
    protected $_object_getters;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

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
        $this->_objects = array();
        return $this;
    }

    public function getActivityFormat()
    {
      return $this->_format;
    }

    public function getActivityIcon()
    {
        return null;
    }

    public function getActivityId()
    {
        return $this->uuid;
    }

    public function getActivityPublished()
    {
        return $this->getObject('lib:date', array('date' => $this->created_on));
    }

    public function getActivityVerb()
    {
        return $this->verb;
    }

    public function getActivityActor()
    {
        return $this->_getObjectActor();
    }

    protected function _getObjectActor()
    {
        if (!$this->_getActivityObject('actor'))
        {
            $actor = $this->_getActivityObject()->setName('actor')->setObjectType('user')->setId($this->created_by)
                          ->setUrl('option=com_users&task=user.edit&id=' . $this->created_by)
                          ->setDisplayName($this->getAuthor()->getName());

            if (!$this->_findObjectActor()) {
                $actor->setDeleted(true)->setValue($this->created_by ? 'Deleted user' : 'Guest user');
            } else {
                $actor->setLink(array('href' => $actor->getUrl()))->translate(false)->setValue($actor->getDisplayName());
            }

            $this->_setObjectActor($actor);
        }

        return $actor;
    }

    public function getActivityObject()
    {
        return $this->_getObjectObject();
    }

    protected function _getObjectObject()
    {
        if (!$this->_getObject('object'))
        {
            $object = $this->_getObject()->setName('object')->setId($this->row)->setObjectType($this->name)
                           ->setUrl('option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row)
                           ->setDisplayName($this->title);

            if (!$this->_findObjectObject()) {
                $object->setDeleted(true);
            }

            $object->setValue($object->getObjectType())->setAttributes(array('class' => array('object')));

            $this->_setObjectObject($object);
        }

        return $object;
    }

    public function getActivityTarget()
    {
        return $this->_getObjectTarget();
    }

    protected function _getObjectTarget()
    {
        return null; // Activities do not have targets by default.
    }

    protected function _getObjectGenerator()
    {
        if (!$generator = $this->_getObject('generator'))
        {
            $generator = $this->_getObject()->setName('generator')->setDisplayName('com_activities')
                              ->setObjectType('component');

            $this->_setObjectGenerator($generator);
        }

        return $generator;
    }

    protected function _getObjectProvider()
    {
        if (!$provider = $this->_getObject('provider'))
        {
            $provider = $this->_getObject()->setName('provider')->setDisplayName('com_activities')
                             ->setObjectType('component');

            $this->_setObjectProvider($provider);
        }

        return $provider;
    }

    protected function _getObjectAction()
    {
        if (!$action = $this->_getObject('action'))
        {
            $action = $this->_getParameter(array('name' => 'action', 'value' => $this->status));
            $this->_setObjectAction($action);
        }

        return $action;
    }

    protected function _getObjectTitle()
    {
        if (!$title = $this->_getObject('title'))
        {
            $title = $this->_getParameter(array('name' => 'title', 'linkable' => true, 'translate' => false));
            $this->_setObjectTitle($title);
        }

        return $title;
    }

    /**
     * Activity object getter.
     *
     * @param string|null $name The name of the activity object to look for, null for requesting a new object.
     *
     * @return ComActivitiesActivityObject|null The activity object, null if there is no object with the provided name.
     */
    protected function _getObject($name = null)
    {
        $object = null;

        if ($name && in_array(array_keys($this->_objects), $name)) {
            $object = $this->_objects[$name];
        } else {
            $object = new ComActivitiesActivityObject('');
        }

        return $object;
    }

    /**
     * Activity object setter.
     *
     * @param ComActivitiesActivityObjectInterface $object The activity object.
     *
     * @return $this
     */
    protected function _setObject(ComActivitiesActivityObjectInterface $object)
    {
        $this->_objects[$object->getName()] = $object;
        return $this;
    }

    /**
     * Activity parameter getter.
     *
     * @param array $config An optional configuration array.
     *
     * @throws RuntimeException
     *
     * @return ComActivitiesActivityObject|null The activity object, null if the activity does not have the requested
     * parameter, i.e. the activity format does not contain the parameter.
     */
    protected function _getParameter(array $config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'linkable'  => false,
            'link'      => array('href' => 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row),
            'translate' => true,
            'value'     => $this->title,
            'attribs'   => array()
        ))->append(array('find' => $config->linkable ? 'object' : false));

        if (!$config->name) {
            throw new RuntimeException('Parameter name is missing.');
        }

        $parameter = null;

        // Only instantiate the parameter if it is contained in the activity format string.
        if (strpos($this->getActivityFormat(), '{'.$config->name.'}') !== false)
        {
            $parameter = $this->_getObject()->setName($config->name);

            if ($config->find !== false)
            {
                $method = '_findObject' . ucfirst($config->find);

                // Make parameter non-linkable if object isn't found and set object as deleted.
                if (method_exists($this, $method) && !$this->$method())
                {
                    $config->linkable = false;
                    $parameter->setDeleted(true);
                }
            }

            if (!$config->linkable) {
                unset($config->link->href);
            }

            $parameter->setLink($config->link->toArray())->translate($config->translate)
                             ->setAttributes($config->attributes->toArray());
        }

        return $parameter;
    }

    public function getActivityObjects()
    {
        $objects = array();

        if (!$getters = $this->_object_getters)
        {
            $getters = array();

            foreach ($this->getMethods() as $method)
            {
                if (strpos($method, '_getObject') === 0 && $method != '_getObject')
                {
                    $name           = strtolower(str_replace('_getObject', '', $method));
                    $getters[$name] = $method;
                }
            }

            $this->_object_getters = $getters;
        }

        foreach ($getters as $name => $method)
        {
            $object = $this->$method();

            if ($object instanceof ComActivitiesActivityObjectInterface)
            {
                $objects[$name] = $object;
            }
        }

        $objects;
    }

    /**
     * Activity object object finder.
     *
     * This method may be overridden for activities persisting objects on storage systems other than local
     * database tables.
     *
     * @return boolean True if found, false otherwise.
     */
    protected function _findObjectObject()
    {
        $signature = $this->_getSignatureObject();

        if (!isset(self::$_found_objects[$signature]))
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

            self::$_found_objects[$signature] = (bool) $result;
        }

        return self::$_found_objects[$signature];
    }

    /**
     * Activity object signature getter.
     *
     * @return string The signature.
     */
    protected function _getSignatureObject()
    {
        return 'object' . $this->package . '.' . $this->name . '.' . $this->row;
    }

    /**
     * Activity actor object finder.
     *
     *
     * @return boolean True if found, false otherwise.
     */
    protected function _findObjectActor()
    {
        $signature = $this->_getSignatureActor();

        if (!isset(self::$_found_objects[$signature])) {
            self::$_found_objects[$signature] = (bool) getObject('user.provider')->load($this->created_by)->getId();
        }

        return self::$_found_objects[$signature];
    }

    /**
     * Activity actor signature getter.
     *
     * @return string The signature.
     */
    protected function _getSignatureActor()
    {
        return 'actor.' . $this->created_by;
    }

    public function __call($method, $arguments)
    {
        if (strpos($method, '_setObject') !== false && count($arguments))
        {
            $object = $arguments[0];

            if ($object instanceof ComActivitiesActivityObjectInterface) {
                return $this->_setObject($object); // Use generic setter.
            }
        }

        parent::__call($method, $arguments);
    }
}

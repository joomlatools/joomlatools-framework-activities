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
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_format           = $config->format;
        $this->_object_table     = $config->object_table;
        $this->_object_column    = $config->object_column;
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
            'format'           => '{actor} {action} {object} {title}',
            'object_table'     => $data->package . '_' . KStringInflector::pluralize($data->name),
            'object_column'    => $data->package . '_' . $data->name . '_id'
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

    public function getActivityObjectActor()
    {
        $actor = $this->_getActivityObject('actor')->setObjectType('user')->setId($this->created_by)
                      ->setUrl('option=com_users&task=user.edit&id=' . $this->created_by)
                      ->setDisplayName($this->getAuthor()->getName());

        if (!$this->getObject('user.provider')->load($this->created_by)->getId()) {
            $actor->setDeleted(true)->setValue($this->created_by ? 'Deleted user' : 'Guest user');
        } else {
            $actor->setLink(array('href' => $actor->getUrl()))->translate(false)->setValue($actor->getDisplayName());
        }

        return $actor;
    }

    public function getActivityObjectObject()
    {
        $object = $this->_getActivityObject('object')->setId($this->row)->setObjectType($this->name)
                       ->setUrl('option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row)
                       ->setDisplayName($this->title);

        if (!$this->_findObjectObject()) {
            $object->setDeleted(true);
        }

        $object->setValue($object->getObjectType())->setAttributes(array('class' => array('object')));

        return $object;
    }

    public function getActivityObjectTarget()
    {
        return null; // Activities do not have targets by default.
    }

    public function getActivityObjectGenerator()
    {
        return $this->_getActivityObject('generator')->setDisplayName('com_activities')->setObjectType('component');
    }

    public function getActivityObjectProvider()
    {
        return $this->_getActivityObject('provider')->setDisplayName('com_activities')->setObjectType('component');
    }


    public function getActivityObjectAction()
    {
        return $this->_getActivityParameter(array('name' => 'action', 'value' => $this->status));
    }

    public function getActivityObjectTitle()
    {
        return $this->_getActivityParameter(array(
            'name'      => 'title',
            'translate' => false,
            'linkable'  => true
        ));
    }

    /**
     * Activity object getter.
     *
     * @param $name The name of the activity object.
     *
     * @return ComActivitiesActivityObject The activity object.
     */
    protected function _getActivityObject($name)
    {
        return new ComActivitiesActivityObject($name);
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
    protected function _getActivityParameter(array $config = array())
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
            $parameter = $this->_getActivityObject($config->name);

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
        if (empty($this->_objects))
        {
            $objects = array();

            foreach ($this->getMethods() as $method)
            {
                if (strpos($method, 'getActivityObject') === 0 && !in_array($method,
                        array('getActivityObject', 'getActivityObjects')))
                {
                    $object = $this->$method();

                    if ($object instanceof ComActivitiesActivityObjectInterface)
                    {
                        $name           = strtolower(str_replace('getActivityObject', '', $method));
                        $objects[$name] = $object;
                    }
                }
            }

            $this->_objects = $objects;
        }

        return $this->_objects;
    }

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
        $signature = $this->_getFoundSignature('object');

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

    protected function _getFoundSignature($type)
    {
        switch($type)
        {
            case 'object':
                $signature = 'object' . $this->package . '.' . $this->name . '.' . $this->row;
                break;
            case 'actor':
                $signature = 'actor.' . $this->created_by;
                break;
        }

        return $signature;
    }
}

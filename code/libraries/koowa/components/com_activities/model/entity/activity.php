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
     * A list of supported activity object names.
     *
     * @var KObjectArray
     */
    protected $_objects;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_format        = $config->format;
        $this->_objects       = KObjectConfig::unbox($config->objects);
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
            'merge'         => true,
            'object_table'  => $data->package . '_' . KStringInflector::pluralize($data->name),
            'object_column' => $data->package . '_' . $data->name . '_id'
        ))->append(array(
                   'objects' => $config->merge ? array(
                           'actor',
                           'object',
                           'generator',
                           'provider') : array()));

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

    public function getPropertyObjects()
    {
        $objects = $this->_objects;

        $parameters = array();

        if (preg_match_all('/\{(.*?)\}/', $this->_format, $matches)) {
            $parameters = $matches[1];
        }

        // Merge format parameters with supported objects.
        $objects = array_merge($objects, $parameters);

        $result = array();

        foreach ($objects as $name)
        {
            $method = 'getActivity'.ucfirst($name);

            if (method_exists($this, $method)) {
                $object = $this->$method();

                if ($object instanceof ComActivitiesActivityObjectInterface) {
                    $result[$name] = $object;
                }
            }
        }

        return $result;
    }

    public function getActivityFormat()
    {
      return $this->format;
    }

    public function getPropertyFormat()
    {
        $parameters = array();

        foreach ($this->objects as $object)
        {
            if ($object->isParameter())
            {
                $parameters[] = $object;
            }
        }

        $translator = $this->getObject('com:activities.activity.translator');

        // Translate format to a readable translated string.
        return $translator->translate($translator->getOverride($this->_format, $parameters));
    }

    public function getActivityIcon()
    {
        return $this->icon;
    }

    public function getPropertyIcon()
    {
        return null; // No icon by default.
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
        return $this->actor;
    }

    public function getPropertyActor()
    {
        $actor = $this->_getObject('actor', array(
            'parameter'   => true,
            'objectType'  => 'user',
            'id'          => $this->created_by,
            'url'         => 'option=com_users&task=user.edit&id=' . $this->created_by,
            'link'        => array('href' => 'option=com_users&task=user.edit&id=' . $this->created_by),
            'displayName' => $this->getAuthor()->getName(),
            'find'        => 'actor'
        ));

        if ($actor->isDeleted()) {
            $actor->setValue($this->created_by ? 'Deleted user' : 'Guest user');
        } else {
            $actor->translate(false)->setValue($actor->getDisplayName());
        }

        return $actor;
    }

    public function getActivityObject()
    {
        return $this->object;
    }

    public function getPropertyObject()
    {
        return $this->_getObject('object', array(
            'parameter'   => true,
            'id'          => $this->row,
            'objectType'  => $this->name,
            'url'         => 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row,
            'displayName' => $this->title,
            'value'       => $this->name,
            'attributes'  => array('class' => array('object')),
            'find'        => 'object'
        ));
    }

    public function getActivityTarget()
    {
        return $this->target;
    }

    public function getPropertyTarget()
    {
        return null; // Activities do not have targets by default.
    }

    public function getActivityGenerator()
    {
        return $this->generator;
    }

    public function getPropertyGenerator()
    {
        return $this->_getObject('generator', array('displayName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityProvider()
    {
        return $this->provider;
    }

    public function getPropertyProvider()
    {
        return $this->_getObject('provider', array('displayName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityAction()
    {
        return $this->_getObject('action', array('value' => $this->status, 'parameter' => true));
    }

    public function getActivityTitle()
    {
        return $this->_getObject('title', array('linkable' => true, 'translate' => false, 'parameter' => true));
    }

    /**
     * Activity object getter.
     *
     * @param string $name   The name of the activity object.
     * @param array  $config An optional configuration array.
     *
     * @return ComActivitiesActivityObject The activity object.
     */
    protected function _getObject($name, $config = array())
    {
        $config = new KObjectConfig($config);

        if ($config->parameter) {
            $object = $this->_getParameter($name, $config);
        } else {
            $object = new ComActivitiesActivityObject($name, $config->toArray());
        }

        // Set object as deleted if related object is not found.
        if ($config->find && !$this->_findObject($config->find)) {
            $object->setDeleted(true);
        }

        return $object;
    }

    /**
     * Activity parameter getter.
     *
     * @param array $config An optional configuration array.
     *
     * @return ComActivitiesActivityObject The activity parameter object.
     */
    protected function _getParameter($name, $config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'linkable'   => false,
            'link'       => array('href' => 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row),
            'translate'  => true,
            'value'      => $this->title,
            'attributes' => array()
        ));

        if ($config->linkable) {
           $config->append(array('find' => 'object'));
        }

        $config->parameter = true;

        // Make parameter non-linkable if related object is not found.
        if ($config->find && !$this->_findObject($config->find)) {
            $config->linkable = false;
        }

        if (!$config->linkable) {
            unset($config->link->href);
            $config->deleted = true;
        }

        return new ComActivitiesActivityObject($name, $config->toArray());
    }

    /**
     * Object finder.
     *
     * @param $name The object name.
     *
     * @return bool True if found, false otherwise.
     */
    protected function _findObject($name)
    {
        $result = false;

        $method = '_findObject' . ucfirst($name);

        if ( method_exists($this, $method)) {
            $result = (bool) $this->$method();
        }

        return $result;
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
        $signature = $this->_getObjectSignature();

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
    protected function _getObjectSignature()
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
        $signature = $this->_getActorSignature();

        if (!isset(self::$_found_objects[$signature]))
        {
            $user                             = $this->getObject('user.provider')->load($this->created_by);
            self::$_found_objects[$signature] = (bool) $user->getId();
        }

        return self::$_found_objects[$signature];
    }

    /**
     * Activity actor signature getter.
     *
     * @return string The signature.
     */
    protected function _getActorSignature()
    {
        return 'actor.' . $this->created_by;
    }
}

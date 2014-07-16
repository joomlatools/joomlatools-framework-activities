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

        $format = $this->getActivityFormat();

        $parameters = array();

        if (preg_match_all('/\{(.*?)\}/', $format, $matches)) {
            $parameters = $matches[1];
        }

        // Merge format parameters with supported objects.
        $objects = array_merge($objects, $parameters);

        $result = array();

        foreach ($objects as $object)
        {
            $method = 'getActivity'.ucfirst($object);

            if (method_exists($this, $method)) {
                $result[$object] = $this->$method();
            }
        }

        return $result;
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
        $this->_object = $this->getObject('lib:object.array');
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
        $actor = $this->_getObject('actor', array(
            'parameter'   => true,
            'objectType'  => 'user',
            'id'          => $this->created_by,
            'url'         => 'option=com_users&task=user.edit&id=' . $this->created_by,
            'displayName' => $this->getAuthor()->getName()
        ));

        if (!$this->_findObjectActor()) {
            $actor->setDeleted(true)->setValue($this->created_by ? 'Deleted user' : 'Guest user');
        } else {
            $actor->setLink(array('href' => $actor->getUrl()))->translate(false)->setValue($actor->getDisplayName());
        }

        return $actor;
    }

    public function getActivityObject()
    {
        $object = $this->_getObject('object', array(
            'parameter'   => true,
            'id'          => $this->row,
            'objectType'  => $this->name,
            'url'         => 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row,
            'displayName' => $this->title
        ));

        if (!$this->_findObjectObject()) {
            $object->setDeleted(true);
        }

        $object->setValue($object->getObjectType())->setAttributes(array('class' => array('object')));

        return $object;
    }

    public function getActivityTarget()
    {
        return null; // Activities do not have targets by default.
    }

    public function getActivityGenerator()
    {
        return $this->_getObject('generator', array('displayName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityProvider()
    {
        return $this->_getObject('provider', array('displayName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityAction()
    {
        return $this->_getObject('action', array('value' => $this->status, 'parameter' => true));
    }

    public function getObjectTitle()
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
        ))->append(array('find' => $config->linkable ? 'object' : false));

        $config->parameter = true;

        if ($config->find !== false)
        {
            $method = '_findObject' . ucfirst($config->find);

            // Make parameter non-linkable if object isn't found and set object as deleted.
            if (method_exists($this, $method) && !$this->$method())
            {
                $config->linkable = false;
                $config->deleted  = true;
            }
        }

        if (!$config->linkable) {
            unset($config->link->href);
        }

        return new ComActivitiesActivityObject($name, $config->toArray());
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
    protected function _getActorSignature()
    {
        return 'actor.' . $this->created_by;
    }
}

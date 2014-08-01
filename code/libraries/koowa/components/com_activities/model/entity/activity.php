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
     * An associative list of found and not found activity objects.
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
            'format'        => '{actor} {action} {object.type} title {object}',
            'object_table'  => $data->package . '_' . KStringInflector::pluralize($data->name),
            'object_column' => $data->package . '_' . $data->name . '_id',
            'objects'       => array('actor', 'object', 'generator', 'provider')
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

    public function getPropertyObjects()
    {
        return array_merge($this->_getObjects($this->_objects), $this->tokens);
    }

    public function getPropertyTokens()
    {
        $labels = array();

        if (preg_match_all('/\{(.*?)\}/', $this->_format, $matches)) {
            $labels = $matches[1];
        }

        return $this->_getObjects(array_unique($labels));
    }

    /**
     * Returns a list of activity objects provided their labels.
     *
     * @param array $labels The object labels.
     *
     * @return array An array containing ComActivitiesActivityObjectInterface objects.
     */
    protected function _getObjects(array $labels = array())
    {
        $result = array();

        foreach ($labels as $label)
        {
            $parts  = explode('.', $label);
            $method = 'getActivity' . ucfirst($parts[0]);

            if (method_exists($this, $method))
            {
                $object = $this->$method();

                if ($object instanceof ComActivitiesActivityObjectInterface)
                {
                    // Deal with dot notation syntax.
                    if (count($parts) === 2)
                    {
                        $property = $parts[1];

                        if ($value = $object->{ 'object' . ucfirst($property)})
                        {
                            $config = array('objectName' => $value);

                            if ($value = $object->{'display' . ucfirst($property)}) {
                                $config['displayName'] = $value;
                            }

                            // Create a new basic and minimal format token object.
                            $object = $this->_getObject($label, $config);
                        } else continue;
                    }

                    $result[$object->getLabel()] = $object;
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
        return $this->getObject('com:activities.activity.translator')
            ->translate($this->_format, array_values($this->tokens));
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
        return $this->_getObject('actor', $this->_getConfig('actor'));
    }

    protected function _actorConfig(KObjectConfig $config)
    {
        $objectName = $this->getAuthor()->getName();
        $translate  = array('displayType');

        if (!$this->_findObjectActor())
        {
            $objectName = $this->created_by ? 'Deleted user' : 'Guest user';
            $translate  = array('displayName', 'displayType');
        }

        return $config->append(array(
            'objectType' => 'user',
            'id'         => $this->created_by,
            'url'        => '?option=com_users&task=user.edit&id=' . $this->created_by,
            'objectName' => $objectName,
            'translate'  => $translate,
            'find'       => 'actor'
        ));
    }

    public function getActivityObject()
    {
        return $this->object;
    }

    public function getPropertyObject()
    {
        return $this->_getObject('object', $this->_getConfig('object'));
    }

    protected function _objectConfig(KObjectConfig $config)
    {
        return $config->append(array(
            'id'         => $this->row,
            'objectName' => $this->title,
            'objectType' => $this->name,
            'url'        => '?option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row,
            'attributes' => array('class' => array('object')),
            'find'       => 'object'
        ));
    }

    protected function _getConfig($object)
    {
        $config = new KObjectConfig();

        $method = '_' . strtolower($object) . 'Config';

        if (method_exists($this, $method)) {
            $config = $this->$method($config);
        }

        return $config;
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
        return $this->_getObject('generator', $this->_getConfig('generator'));
    }

    protected function _generatorConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityProvider()
    {
        return $this->provider;
    }

    public function getPropertyProvider()
    {
        return $this->_getObject('provider', $this->_getConfig('provider'));
    }

    protected function _providerConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityAction()
    {
        return $this->_getObject('action', $this->_getConfig('action'));
    }

    protected function _actionConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => $this->status));
    }

    /**
     * Activity object getter.
     *
     * @param string $label The label of the activity object.
     * @param array $config An optional configuration array.
     *
     * @return ComActivitiesActivityObject The activity object.
     */
    protected function _getObject($label, $config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'displayType' => $config->objectType,
            'displayName' => $config->objectName,
            'attributes'  => array()
        ));

        if (!$config->translate && $config->translate !== false) {
            $config->translate = array('displayName', 'displayType');
        }

        if (is_string($config->url))
        {
            $parts = parse_url($config->url);

            // Check if the URL should be routed or not.
            if (isset($parts['scheme']) || isset($parts['path']) ) {
                $identifier = 'lib:http.url';
            } else {
                $identifier = 'lib:dispatcher.router.route';
            }

            $config->url = $this->getObject($identifier, array('url' => $config->url));
        }

        // Make object non-linkable and set it as deleted if related entity is not found.
        if ($config->find && !$this->_findObject($config->find)) {
            $config->url     = null;
            $config->deleted = true;
        }

        if ($translate = $config->translate)
        {
            $translator = $this->getObject('translator');
            $translate  = (array) KObjectConfig::unbox($translate);

            foreach ($translate as $property)
            {
                if ($config->{$property}) {
                    $config->{$property} = $translator->translate($config->{$property});
                }
            }
        }

        // Cleanup config file.
        foreach (array('translate', 'find') as $property) {
            unset($config[$property]);
        }

        return new ComActivitiesActivityObject($label, $config->toArray());
    }

    /**
     * Object finder.
     *
     * @param $label The object label.
     *
     * @return bool True if found, false otherwise.
     */
    protected function _findObject($label)
    {
        $result = false;

        $method = '_findObject' . ucfirst($label);

        if (method_exists($this, $method)) {
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

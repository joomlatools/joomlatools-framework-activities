<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/nooku/nooku-activities for the canonical source repository
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
        $class = $manager->getClass($config->object_identifier);

        if ($class == get_class())
        {
            $identifier            = $config->object_identifier->toArray();
            $identifier['package'] = $config->data->package;

            if ($class = $manager->getClass($identifier, false)) {
                return $manager->getObject($identifier, $config->toArray());
            }
        }

        return new $class($config);
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

        if (!$this->_format) {
            $this->format; // Try generating the actual activity format. This is where computed short formats are calculated.
        }

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
                            $config = array('objectName' => $value, 'internal' => true);

                            // If display property is set use it and disable properties translations.
                            if ($value = $object->{'display' . ucfirst($property)})
                            {
                                $config['displayName'] = $value;
                                $config['translate']   = false;
                            }

                            // Create a new basic and minimal format token object.
                            $object = $this->_getObject($config);
                        } else continue;
                    }

                    $result[$label] = $object;
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
            ->translate($this->_format, $this->tokens);
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
        return $this->_getObject($this->_getConfig('actor'));
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
        return $this->_getObject($this->_getConfig('object'));
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
        return $this->_getObject($this->_getConfig('generator'));
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
        return $this->_getObject($this->_getConfig('provider'));
    }

    protected function _providerConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => 'com_activities', 'objectType' => 'component'));
    }

    public function getActivityAction()
    {
        return $this->_getObject($this->_getConfig('action'));
    }

    protected function _actionConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => $this->status));
    }

    /**
     * Activity object getter.
     *
     * @param array $config An optional configuration array.
     *
     * @return ComActivitiesActivityObject The activity object.
     */
    protected function _getObject($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('attributes' => array()));

        $defaults = array();

        // Determine default properties and their values.
        foreach ($config as $key => $value)
        {
            if (strpos($key, 'object') === 0 && isset($value)) {
                $defaults['display' . ucfirst(substr($key, 6))] = $value;
            }
        }

        if ($defaults)
        {
            // Append default properties.
            $config->append($defaults);

            // Set default translatable properties.
            if (!$config->translate && $config->translate !== false) {
                $config->translate = array_keys($defaults);
            }
        }

        if (is_string($config->url))
        {
            $config->url = $this->_getUrl($config->url);
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

        if ($config->image instanceof KObjectConfig)
        {
            $config->image->url = $this->_getUrl($config->image->url);
            $config->image      = $this->getObject('com:activities.activity.medialink', array('data' => $config->image));
        }

        // Cleanup config.
        foreach (array('translate', 'find') as $property) {
            unset($config->$property);
        }

        return $this->getObject('com:activities.activity.object', array('data' => $config));
    }

    /**
     * Url getter.
     *
     * @param string $url The URL.
     * @param bool|null $route Whether or not the Url should be routed. If null is passed, the method automatically
     * determines if the Url should be routed based on the provided Url.
     *
     * @return KHttpUrlInterface The Url.
     */
    protected function _getUrl($url, $route = null)
    {
        if (is_string($url))
        {
            if (is_null($route))
            {
                $parts = parse_url($url);

                if (!empty($parts['path']) || !empty($parts['scheme'])) {
                    $route = false;
                } else {
                    $route = true;
                }
            }

            if ($route) {
                $url = $this->getObject('lib:dispatcher.router.route', array('url' => $url));
            } else {
                $url = $this->getObject('lib:http.url', array('url' => $url));
            }
        }

        return $url;
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

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Entity.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesModelEntityActivity extends KModelEntityRow implements KObjectInstantiable, ComActivitiesActivityInterface
{
    /**
     * An associative list of found and not found activity objects.
     *
     * @var array
     */
    static protected $_found_objects = array();

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
     * A list of supported activity object names.
     *
     * @var array
     */
    protected $_objects;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
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
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
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
     * Instantiates the object.
     *
     * @param KObjectConfigInterface  $config  Configuration options.
     * @param KObjectManagerInterface $manager A KObjectManagerInterface object.
     *
     * @return KObjectInterface
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        $identifier            = $config->object_identifier->toArray();
        $identifier['path']    = array('model', 'entity', 'activity');
        $identifier['package'] = $config->data->package;
        $identifier['name']    = $config->data->name;

        $identifiers = array($identifier);

        $identifier['name'] = $identifier['package'];

        array_push($identifiers, $identifier);

        foreach ($identifiers as $identifier)
        {
            if ($manager->getClass($identifier, false)) {
                return $manager->getObject($identifier, $config->toArray());
            }
        }

        $class = $manager->getClass($config->object_identifier);

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

    /**
     * Get the activity format.
     *
     * An activity format consist on a template for rendering activity messages.
     *
     * @return string The activity string format.
     */
    public function getActivityFormat()
    {
        return $this->format;
    }

    /**
     * Get the activity icon.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See icon property.
     *
     * @return ComActivitiesActivityMedialinkInterface|null The activity icon, null if the activity does not have an
     *                                                      icon.
     */
    public function getActivityIcon()
    {
        return $this->icon;
    }

    /**
     * Get the activity id.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See id property.
     *
     * @return string The activity ID.
     */
    public function getActivityId()
    {
        return $this->uuid;
    }

    /**
     * Get the activity published date.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See published property.
     *
     * @return KDateInterface The published date.
     */
    public function getActivityPublished()
    {
        return $this->getObject('lib:date', array('date' => $this->created_on));
    }

    /**
     * Get the activity verb.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See verb property.
     *
     * @return string The activity verb.
     */
    public function getActivityVerb()
    {
        return $this->verb;
    }

    /**
     * Get the activity actor.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See actor property.
     *
     * @return ComActivitiesActivityObjectInterface The activity actor object.
     */
    public function getActivityActor()
    {
        return $this->actor;
    }

    /**
     * Get the activity object.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See object property.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity object, null if the activity does not have an
     *                                                   object.
     */
    public function getActivityObject()
    {
        return $this->object;
    }

    /**
     * Get the activity target.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See target property.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity target object, null if the activity does no have
     *                                                   a target.
     */
    public function getActivityTarget()
    {
        return $this->target;
    }

    /**
     * Get the activity generator.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See generator property.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity generator object, null if the activity does not
     *                                                   have a generator.
     */
    public function getActivityGenerator()
    {
        return $this->generator;
    }

    /**
     * Get the activity provider.
     *
     * @link http://activitystrea.ms/specs/json/1.0/#activity See provider property.
     *
     * @return ComActivitiesActivityObjectInterface|null The activity provider object, null if the activity does not
     *                                                   have a provider.
     */
    public function getActivityProvider()
    {
        return $this->provider;
    }

    /**
     * Get the activity action.
     *
     * @return ComActivitiesActivityObjectInterface
     */
    public function getActivityAction()
    {
        return $this->_getObject($this->_getConfig('action'));
    }

    /**
     * Get the format property.
     *
     * @return string
     */
    public function getPropertyFormat()
    {
        return $this->getObject('com:activities.activity.translator')->translate($this->_format, $this->tokens);
    }

    /**
     * Get the verb property.
     *
     * @return string
     */
    public function getPropertyVerb()
    {
        return $this->action;
    }

    /**
     * Get the icon property.
     *
     * @return null
     */
    public function getPropertyIcon()
    {
        return null; // No icon by default.
    }

    /**
     * Get the actor property.
     *
     * @return null
     */
    public function getPropertyActor()
    {
        return $this->_getObject($this->_getConfig('actor'));
    }

    /**
     * Get the target property.
     *
     * @return null
     */
    public function getPropertyTarget()
    {
        return null; // Activities do not have targets by default.
    }

    /**
     * Get the object property.
     *
     * @return ComActivitiesActivityObject
     */
    public function getPropertyObject()
    {
        return $this->_getObject($this->_getConfig('object'));
    }

    /**
     * Get the generator property.
     *
     * @return ComActivitiesActivityObject
     */
    public function getPropertyGenerator()
    {
        return $this->_getObject($this->_getConfig('generator'));
    }

    /**
     * Get the provider property.
     *
     * @return ComActivitiesActivityObject
     */
    public function getPropertyProvider()
    {
        return $this->_getObject($this->_getConfig('provider'));
    }

    /**
     * Get the activity objects.
     *
     * @return array An array containing ComActivitiesActivityObjectInterface objects.
     */
    public function getPropertyObjects()
    {
        return array_merge($this->_getObjects($this->_objects), $this->tokens);
    }

    /**
     * Get the activity tokens.
     *
     * @return array An array containing ComActivitiesActivityObjectInterface objects.
     */
    public function getPropertyTokens()
    {
        $labels = array();

        // Try generating the actual activity format. This is where computed short formats are calculated.
        if (!$this->_format) {
            $this->format;
        }

        if (preg_match_all('/\{(.*?)\}/', $this->_format, $matches)) {
            $labels = $matches[1];
        }

        return $this->_getObjects(array_unique($labels));
    }

    /**
     * Prevent setting the package property.
     *
     * @return array An array containing ComActivitiesActivityObjectInterface objects.
     */
    public function setPropertyPackage($value)
    {
        if ($this->package && $this->package != $value) {
            throw new RuntimeException('Entity package cannot be modified.');
        }

        return $value;
    }

    /**
     * Prevent removing the package property.
     *
     * @param string $name The property name.
     *
     * @throws RuntimeException When attempting to remove the package property.
     * @return KDatabaseRowAbstract
     */
    public function removeProperty($name)
    {
        if ($name == 'package') {
            throw new RuntimeException('Entity package property cannot be removed.');
        }

        return parent::removeProperty($name);
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
                        }
                        else continue;
                    }

                    $result[$label] = $object;
                }
            }
        }

        return $result;
    }

    /**
     * Get an activity object
     *
     * @param array $config An optional configuration array. The configuration array may contain activity object data as
     *                      defined by ComActivitiesActivityObjectInterface. Additionally the following parameters may
     *                      be passed in the configuration array:
     *                      <br><br>
     *                      - find (string): the label of an object to look for. If not found the object being created
     *                      is set as deleted (with its deleted property set to true) and non-linkable (with its url
     *                      property set to null). A call to a _findObjectLabel method will be attempted for determining
     *                      if an object with label as defined by Label exists. See {@link _findObjectActor()} as an
     *                      example.
     *                      <br><br>
     *                      - translate (array): a list of property names to be translated. By default all properties
     *                      containing the display prefix are set as translatables.
     *
     * @return ComActivitiesActivityObject The activity object.
     */
    protected function _getObject($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'attributes' => array()
        ));

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

        if (is_string($config->url)) {
            $config->url = $this->_getRoute($config->url);
        }

        // Make object non-linkable and set it as deleted if related entity is not found.
        if ($config->find && !$this->_findObject($config->find))
        {
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
            if (is_string($config->image->url)) {
                $config->image->url = $this->_getRoute($config->image->url);
            }

            $config->image      = $this->getObject('com:activities.activity.medialink', array('data' => $config->image));
        }

        // Cleanup config.
        foreach (array('translate', 'find') as $property) {
            unset($config->$property);
        }

        return $this->getObject('com:activities.activity.object', array('data' => $config));
    }

    /**
     * Get an activity object config.
     *
     * @param string $object The object name.
     *
     * @return KObjectConfig
     */
    protected function _getConfig($object)
    {
        $config = new KObjectConfig();

        $method = '_' . strtolower($object) . 'Config';

        if (method_exists($this, $method)) {
            $config = $this->$method($config);
        }

        return $config;
    }

    /**
     * Get the actor config.
     *
     * @param KObjectConfig $config The actor config.
     *
     * @return KObjectConfig
     */
    protected function _actorConfig(KObjectConfig $config)
    {
        $objectName = $this->getAuthor()->getName();
        $translate  = array('displayType');

        if (!$this->_findObjectActor())
        {
            $objectName = 'Deleted user';
            $translate[]  = 'displayName';
        }

        $config->append(array(
            'objectType' => 'user',
            'id'         => $this->created_by,
            'url'        => 'option=com_users&task=user.edit&id=' . $this->created_by,
            'objectName' => $objectName,
            'translate'  => $translate,
            'find'       => 'actor'
        ));

        return $config;
    }

    /**
     * Get the object config.
     *
     * @param KObjectConfig $config The object config.
     *
     * @return KObjectConfig
     */
    protected function _objectConfig(KObjectConfig $config)
    {
        $config->append(array(
            'id'         => $this->row,
            'objectName' => $this->title,
            'objectType' => $this->name,
            'url'        => 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row,
            'attributes' => array('class' => array('object')),
            'find'       => 'object'
        ));

        return $config;
    }

    /**
     * Get the generator config.
     *
     * @param KObjectConfig $config The generator config.
     *
     * @return KObjectConfig
     */
    protected function _generatorConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => 'com_activities', 'objectType' => 'component'));
    }

    /**
     * Get the generator config.
     *
     * @param KObjectConfig $config The generator config.
     *
     * @return KObjectConfig
     */
    protected function _providerConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => 'com_activities', 'objectType' => 'component'));
    }

    /**
     * Get the action config.
     *
     * @param KObjectConfig $config The action config.
     *
     * @return KObjectConfig
     */
    protected function _actionConfig(KObjectConfig $config)
    {
        return $config->append(array('objectName' => $this->status));
    }

    /**
     * Get the activity object signature.
     *
     * @return string The signature.
     */
    protected function _getObjectSignature()
    {
        return 'object' . $this->package . '.' . $this->name . '.' . $this->row;
    }

    /**
     * Get the activity actor signature.
     *
     * @return string The signature.
     */
    protected function _getActorSignature()
    {
        return 'actor.' . $this->created_by;
    }

    /**
     * Find an activity object.
     *
     * @param string $label The object label.
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
     * Find the activity object object.
     *
     * This method may be overridden for activities persisting objects on storage systems other than local database
     * tables.
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
     * Find the activity actor object.
     *
     * @return boolean True if found, false otherwise.
     */
    protected function _findObjectActor()
    {
        $signature = $this->_getActorSignature();

        if (!isset(self::$_found_objects[$signature]))
        {
            $user                             = $this->getObject('user.provider')->fetch($this->created_by);
            self::$_found_objects[$signature] = is_null($user) ? false : true;
        }

        return self::$_found_objects[$signature];
    }

    /**
     * Route getter.
     *
     * @param string $url The URL to route.
     *
     * @return KHttpUrl The routed URL object.
     */
    protected function _getRoute($url)
    {
        if (!is_string($url)) throw new InvalidArgumentException('The URL must be a query string');

        return $this->getObject('lib:dispatcher.router.route', array('url' => array('query' => $url)));
    }
}

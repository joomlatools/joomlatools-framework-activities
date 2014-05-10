<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesModelEntityActivity extends KModelEntityRow implements ComActivitiesModelEntityActivityInterface, KObjectInstantiable
{
    /**
     * Message object identifier.
     *
     * @var mixed
     */
    protected $_message;

    /**
     * Message parameter object identifier.
     *
     * @param mixed
     */
    protected $_parameter;

    /**
     * Holds a list of loaded scripts.
     *
     * @var bool
     */
    static protected $_scripts_loaded;

    /**
     * @var array A list of required columns.
     */
    protected $_required = array('package', 'name', 'action', 'title', 'status');

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_message    = $config->message;
        $this->_parameter  = $config->parameter;
        $this->_translator = $config->translator;

        self::$_scripts_loaded = array();
    }


    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'parameter' => 'com:activities.message.parameter',
            'message'   => 'com:activities.message'
        ));

        parent::_initialize($config);
    }

    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if (!$package = $config->data->package) {
            throw new RuntimeException('Unable to determine the activity package');
        }

        if ($config->object_identifier->class == get_class())
        {
            $identifier            = $config->object_identifier->toArray();
            $identifier['package'] = $package;

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

        $translator = $this->getObject('translator');

        if (!in_array($this->application, array('admin', 'site')))
        {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage($translator->translate('Invalid application value'));
            return false;
        }

        if (!in_array($this->type, array('com')))
        {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage($translator->translate('Invalid type value'));
            return false;
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
                $this->setStatusMessage($translator->translate('Missing required data'));
                return false;
            }
        }

        return parent::save();
    }

    public function setProperty($name, $value, $modified = true)
    {
        // Map metadata to parameters (POST data contains a metadata key not parameters).
        if ($name == 'metadata') {
            $name = 'parameters';
        }

        return parent::setProperty($name, $value, $modified);
    }

    public function setPropertyVerb($value)
    {
        $this->setProperty('action', $value);
    }

    public function removeProperty($name)
    {
        if ($name == 'package') {
            throw new RuntimeException('Entity package property cannot be removed.');
        }

        return parent::removeProperty($name);
    }

    public function getPropertyVerb()
    {
        return $this->getProperty('action');
    }

    public function getPropertyMetadata()
    {
        return  $this->getParameters();
    }

    public function setPropertyPackage($value)
    {
        if ($this->package && $this->package != $value) {
            throw new RuntimeException('Entity package cannot be modified.');
        }

        return $value;
    }

    public function getMessage()
    {
        $config = array('format' => $this->_getMessageFormat());

        $identifier = (string) $this->getIdentifier();

        if (!in_array($identifier, self::$_scripts_loaded))
        {
            if ($scripts = $this->_getMessageScripts()) {
                $config['scripts'] = $scripts;
            }

            self::$_scripts_loaded[] = $identifier;
        }

        $message = $this->getObject($this->_message, $config);
        $message->getParameters()->setData($this->_getMessageParameters());

        return $message;
    }

    public function getIcon()
    {
        $classes = array(
            'publish'   => 'icon-ok-circle',
            'unpublish' => 'icon-remove-circle',
            'trash'     => 'icon-trash',
            'add'       => 'icon-plus-sign',
            'edit'      => 'icon-edit',
            'delete'    => 'icon-remove',
            'archive'   => 'icon-inbox');

        // Default.
        $icon = 'icon-task';

        $verb = $this->verb;

        if (in_array($verb, array_keys($classes))) {
            $icon = $classes[$verb];
        }

        return $icon;
    }

    public function setEntity(ComActivitiesModelEntityActivity $activity)
    {
        $this->_entity = $activity;

        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function actorExists()
    {
        return $this->_resourceExists(array('table' => 'users', 'column' => 'id', 'value' => $this->created_by));
    }

    public function objectExists()
    {
        return $this->_resourceExists();
    }

    public function targetExists()
    {
        return false; // Activities don't have targets by default.
    }

    public function getActorUrl()
    {
        $url = null;

        if ($this->created_by) {
            $url = 'option=com_users&task=user.edit&id=' . $this->created_by;
        }

        return $url;
    }

    public function getObjectUrl()
    {
        $url = null;

        if ($this->package && $this->name && $this->row) {
            $url = 'option=com_' . $this->package . '&task=' . $this->name . '.edit&id=' . $this->row;
        }

        return $url;
    }

    public function getTargetUrl()
    {
        return null; // Non-linkable as no target by default.
    }

    public function hasTarget()
    {
        return false; // Activities don't have targets by default.
    }

    public function getObjectType()
    {
        return $this->name;
    }

    public function getTargetId()
    {
        return null; // Activities don't have targets by default.
    }

    public function getTargetType()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Determines if a given resource exists.
     *
     * @param array $config An optional configuration array.
     *
     * @return bool True if it exists, false otherwise.
     */
    protected function _resourceExists($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'table'  => $this->package . '_' . KStringInflector::pluralize($this->name),
            'column' => $this->package . '_' . $this->name . '_' . 'id',
            'value'  => $this->row));

        $db = $this->getTable()->getAdapter();

        $query = $this->getObject('lib:database.query.select');
        $query->columns('COUNT(*)')->table($config->table)->where($config->column . ' = :value')
            ->bind(array('value' => $config->value));

        // Need to catch exceptions here as table may not longer exist.
        try {
            $result = $db->select($query, KDatabase::FETCH_FIELD);
        } catch (Exception $e) {
            $result = 0;
        }

        return (bool) $result;
    }

    /**
     * Message key getter.
     *
     * An activity message format is a compact representation of the activity which also provides information
     * about the parameters it may contain.
     *
     * @return string The activity message format.
     */
    protected function _getMessageFormat()
    {
        return '{actor} {action} {object} {title}';
    }

    /**
     * Message scripts getter.
     *
     * Returns
     *
     * @return mixed
     */
    protected function _getMessageScripts()
    {
        // No scripts by default.
        return null;
    }

    protected function _getMessageParameters()
    {
        $parameters = array();

        if (preg_match_all('/\{(.*?)\}/', $this->_getMessageFormat(), $matches) !== false)
        {
            foreach ($matches[1] as $parameter)
            {
                $method = '_getMessage' . ucfirst($parameter);

                if (method_exists($this, $method))
                {
                    $config = new KObjectConfig();
                    $this->$method($config);

                    $config->name = $parameter;
                    $parameters[] = $this->getObject($this->_parameter, $config->toArray());
                }
            }
        }

        return $parameters;
    }

    /**
     * Get the actor activity message parameter configuration
     *
     * @param KObjectConfig $config The message parameter configuration object.
     */
    protected function _getMessageActor(KObjectConfig $config)
    {
        if ($this->actorExists())
        {
            $config->url = $this->getActorUrl();
            $value  = $this->created_by_name;
        }
        else
        {
            $value = $this->created_by ? 'Deleted user' : 'Guest user';
            $config->translate = true;
        }

        $config->value = $value;
    }

    /**
     * Get the action activity message parameter configuration.
     *
     * @param KObjectConfig $config The activity message parameter configuration object.
     */
    protected function _getMessageAction(KObjectConfig $config)
    {
        $config->append(array(
            'value'      => $this->status,
            'translate' => true));
    }

    /**
     * Get the object activity message parameter configuration.
     *
     * @param KObjectConfig $config The activity message parameter configuration object.
     */
    protected function _getMessageObject(KObjectConfig $config)
    {
        $config->append(array(
            'translate'  => true,
            'value'       => $this->name,
            'attributes' => array('class' => array('object')),
        ));
    }

    /**
     * Get the title activity message parameter configuration.
     *
     * @param KObjectConfig $config The activity message parameter configuration object.
     */
    protected function _getMessageTitle(KObjectConfig $config)
    {
        $config->append(array(
            'attributes' => array(),
            'translate'  => false,
            'value'      => $this->title
        ));

        if (!$config->url && $this->objectExists() && ($url = $this->getObjectUrl())) {
            $config->url = $url;
        }

        if ($this->status == 'deleted') {
            $config->attributes = array('class' => array('deleted'));
        }
    }
}

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
     * Message variable object identifier.
     *
     * @param mixed
     */
    protected $_variable;

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
        $this->_variable   = $config->variable;
        $this->_translator = $config->translator;

        self::$_scripts_loaded = array();
    }


    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'variable' => 'com:activities.message.variable',
            'message'  => 'com:activities.message'));
        parent::_initialize($config);
    }

    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if (!$package = $config->activity->package) {
            throw new RuntimeException('Unable to determine the activity package');
        }

        // Set package on entity.
        $config->append(array('data' => array('package' => $package)));

        if ($config->object_identifier->class == get_class())
        {
            $identifier            = $config->object_identifier->toArray();
            $identifier['package'] = $package;

            if ($class = $manager->getClass($identifier, false))
            {
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

    public function setPropertyVerb($value)
    {
        $this->setProperty('action', $value);
    }

    public function getPropertyVerb()
    {
        return $this->getProperty('action');
    }

    public function getPropertyMetadata()
    {
        return  $this->getParameters();
    }

    public function setProperty($name, $value, $modified = true)
    {
        // Map metadata to parameters (POST data contains a metadata key not parameters).
        if ($name == 'metadata') {
            $name = 'parameters';
        }

        return parent::setProperty($name, $value, $modified);
    }

    public function setPropertyPackage($value)
    {
        if ($this->package && $this->package != $value) {
            throw new RuntimeException('Entity package cannot be modified.');
        }

        return $value;
    }

    public function removeProperty($name)
    {
        if ($name == 'package') {
            throw new RuntimeException('Entity package property cannot be removed.');
        }

        return parent::removeProperty($name);
    }

    public function getMessage()
    {
        $config = array('key'  => $this->_getMessageKey());

        $identifier = (string) $this->getIdentifier();

        if (!in_array($identifier, self::$_scripts_loaded))
        {
            if ($scripts = $this->_getMessageScripts()) {
                $config['scripts'] = $scripts;
            }

            self::$_scripts_loaded[] = $identifier;
        }

        $message = $this->getObject($this->_message, $config);
        $message->getVariables()->setData($this->_getMessageVariables());

        return $message;
    }

    protected function _getMessageVariables()
    {
        $variables = array();

        if (preg_match_all('/\{(.*?)\}/', $this->_getMessageKey(), $matches) !== false)
        {
            foreach ($matches[1] as $variable)
            {
                $method = '_set' . ucfirst($variable);

                if (method_exists($this, $method))
                {
                    $config = new KObjectConfig();
                    $this->$method($config);
                    $config->label = $variable;
                    $variables[] = $this->getObject($this->_variable, $config->toArray());
                }
            }
        }

        return $variables;
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
     * An activity message key is a compact representation of the activity text which also provides information
     * about the variables it may contain. This key is used in the same way Joomla! translation keys are
     * used for translating text to other languages.
     *
     * @return string The activity message key.
     */
    protected function _getMessageKey()
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

    /**
     * Actor translator variable configuration setter.
     *
     * @param KObjectConfig $config The message variable configuration object.
     */
    protected function _setActor(KObjectConfig $config)
    {
        if ($this->actorExists())
        {
            $config->url = $this->getActorUrl();
            $text         = $this->created_by_name;
        }
        else
        {
            $text              = $this->created_by ? 'Deleted user' : 'Guest user';
            $config->translate = true;
        }

        $config->text = $text;
    }

    /**
     * Action activity message variable configuration setter.
     *
     * @param KObjectConfig $config The activity message variable configuration object.
     */
    protected function _setAction(KObjectConfig $config)
    {
        $config->append(array(
            'text'      => $this->status,
            'translate' => true));
    }

    /**
     * Object activity message variable configuration setter.
     *
     * @param KObjectConfig $config The activity message variable configuration object.
     */
    protected function _setObject(KObjectConfig $config)
    {
        $config->append(array(
            'translate'  => true,
            'text'       => $this->name,
            'attributes' => array('class' => array('object')),
        ));
    }

    /**
     * Title activity message variable configuration setter.
     *
     * @param KObjectConfig $config The activity message variable configuration object.
     */
    protected function _setTitle(KObjectConfig $config)
    {
        $config->append(array(
            'attributes' => array(),
            'translate'  => false,
            'text'       => $this->title
        ));

        if (!$config->url && $this->objectExists() && ($url = $this->getObjectUrl())) {
            $config->url = $url;
        }

        if ($this->status == 'deleted') {
            $config->attributes = array('class' => array('deleted'));
        }
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
}

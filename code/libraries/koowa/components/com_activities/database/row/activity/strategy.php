<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Database Row Strategy
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesDatabaseRowActivityStrategy extends KObject implements ComActivitiesDatabaseRowActivityStrategyInterface
{
    /**
     * The activity message object.
     *
     * @var ComActivitiesMessageInterface
     */
    protected $_message;

    /**
     * The activity row object.
     *
     * @var ComActivitiesDatabaseRowActivity
     */
    protected $_row;

    /**
     * Activity message parameter identifier.
     *
     * @param mixed
     */
    protected $_parameter;

    /**
     * Determines if scripts are already loaded of not.
     *
     * @var bool
     */
    static protected $_scripts_loaded = array();

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!$config->row instanceof ComActivitiesDatabaseRowActivity) {
            throw new BadMethodCallException('The activity database row object is missing.');
        }

        if ($config->row) {
            $this->setRow($config->row);
        }

        $this->_message    = $config->message;
        $this->_parameter  = $config->parameter;
        $this->_translator = $config->translator;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'parameter' => 'com:activities.message.parameter',
            'message'   => 'com:activities.message'));
        parent::_initialize($config);
    }

    public function getMessage()
    {
        $config = array('string'  => $this->_getString());

        $identifier = (string) $this->getIdentifier();

        if (!in_array($identifier, self::$_scripts_loaded))
        {
            $config['scripts'] = $this->_getScripts();
            self::$_scripts_loaded[] = $identifier;
        }

        $message = $this->getObject($this->_message, $config);
        $message->getParameters()->setData($this->_getParameters());

        return $message;
    }

    protected function _getParameters()
    {
        $parameters = array();

        if (preg_match_all('/\{(.*?)\}/', $this->_getString(), $matches) !== false)
        {
            foreach ($matches[1] as $parameter)
            {
                $method = '_set' . ucfirst($parameter);

                if (method_exists($this, $method))
                {
                    $config = new KObjectConfig();
                    $this->$method($config);
                    $config->label = $parameter;
                    $parameters[] = $this->getObject($this->_parameter, $config->toArray());
                }
            }
        }

        return $parameters;
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

        $db = $this->getRow()->getTable()->getAdapter();

        $query = $this->getObject('koowa:database.query.select');
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
     * Returns activity row column values if a matching column for the requested key is found.
     *
     * @param string $key The requested key.
     *
     * @return mixed The activity row column value if a matching column is found for the requested key, null otherwise.
     */
    public function __get($key)
    {
        $row = $this->getRow();
        return isset($row->{$key}) ? $row->{$key} : null;
    }

    /**
     * Activity string getter.
     *
     * An activity string is a compact representation of the activity text which also provides information
     * about the variables it may contain. These are used in the same way Joomla! translation keys are
     * used for translating text to other languages.
     *
     * @return string The activity string.
     */
    protected function _getString()
    {
        return '{actor} {action} {object} {title}';
    }

    /**
     * @return null
     */
    protected function _getScripts()
    {
        // No scripts by default.
        return null;
    }

    public function getIcon()
    {
        $classes = array(
            'publish'   => 'icon-ok',
            'unpublish' => 'icon-eye-close',
            'trash'     => 'icon-trash',
            'add'       => 'icon-plus-sign',
            'edit'      => 'icon-edit',
            'delete'    => 'icon-remove',
            'archive'   => 'icon-inbox');

        // Default.
        $icon = 'icon-task';

        $action = $this->action;

        if (in_array($action, array_keys($classes))) {
            $icon = $classes[$action];
        }

        return $icon;
    }

    /**
     * Actor translator parameter configuration setter.
     *
     * @param KObjectConfig $config The translator parameter configuration object.
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
     * Action translator parameter configuration setter.
     *
     * @param KObjectConfig $config The translator parameter configuration object.
     */
    protected function _setAction(KObjectConfig $config)
    {
        $config->append(array(
            'text'      => $this->status,
            'translate' => true));
    }

    /**
     * Object translator parameter configuration setter.
     *
     * @param KObjectConfig $config The translator parameter configuration object.
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
     * Title translator parameter configuration setter.
     *
     * @param KObjectConfig $config The translator parameter configuration object.
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

    public function setRow(ComActivitiesDatabaseRowActivity $row)
    {
        $this->_row = $row;
        return $this;
    }

    public function getRow()
    {
        return $this->_row;
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
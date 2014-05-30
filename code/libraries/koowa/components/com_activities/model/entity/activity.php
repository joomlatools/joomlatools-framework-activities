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
     * The message format.
     *
     * @var string
     */
    protected $_format;

    /**
     * Message parameters
     *
     * @param mixed
     */
    protected $_parameters;

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
        $config->append(array(
            'format'        => '{actor} {action} {object} {title}',
            'object_table'  => $config->data->package . '_' . KStringInflector::pluralize($config->data->name),
            'object_column' => $config->data->package . '_' . $config->data->name . '_id'
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

        $translator = $this->getObject('translator');

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

    /**
     * Verb is an alias for action
     *
     * @return mixed
     */
    public function getPropertyVerb()
    {
        return $this->getProperty('action');
    }

    /**
     * Verb is an alias for action
     *
     * @param $value
     */
    public function setPropertyVerb($value)
    {
        $this->setProperty('action', $value);
    }

    /**
     * Get the activity string format
     *
     * The string format is a compact representation of the activity which also provides information about the
     * parameters it may contain.
     *
     * @return string The activity string format.
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Looks for the activity actor.
     *
     * @return boolean True if found, false otherwise.
     */
    public function findActor()
    {
        return (bool) $this->getObject('user.provider')->load($this->created_by)->getId();
    }

    /**
     * Activity actor id getter.
     *
     * @return mixed The activity actor id.
     */
    public function getActorId()
    {
        return $this->created_by;
    }

    /**
     * Activity actor URL getter.
     *
     * @return mixed The activity actor url.
     */
    public function getActorUrl()
    {
        return 'option=com_users&task=user.edit&id=' . $this->created_by;
    }

    /**
     * Activity actor type getter.
     *
     * @return mixed The activity actor type.
     */
    public function getActorType()
    {
        return 'user';
    }

    /**
     * Looks for the activity object.
     *
     * @return boolean True if found, false otherwise.
     */
    public function findObject()
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

        return (bool) $result;
    }

    /**
     * Activity object id getter.
     *
     * @return mixed The activity object id.
     */
    public function getObjectId()
    {
        return $this->row;
    }

    /**
     * Activity object URL getter.
     *
     * @return mixed The activity object url.
     */
    public function getObjectUrl()
    {
        return 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row;
    }

    /**
     * Activity object type getter.
     *
     * @return mixed The activity object type.
     */
    public function getObjectType()
    {
        return $this->name;
    }

    /**
     * Looks for the activity target.
     *
     * @return bool|null True if found, false if not found, null if the activity has no target.
     */
    public function findTarget()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Activity target id getter.
     *
     * @return mixed The activity target id.
     */
    public function getTargetId()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Activity target URL getter.
     *
     * @return mixed The activity target URL.
     */
    public function getTargetUrl()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Activity target type getter.
     *
     * @return mixed The activity target type.
     */
    public function getTargetType()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Get the activity parameters
     *
     * @return array The activity parameters.
     */
    public function getParameters()
    {
       if(!isset($this->_parameters))
       {
           $this->_parameters = array();

           if (preg_match_all('/\{(.*?)\}/', $this->getFormat(), $matches) !== false)
           {
               $translator = $this->getObject('translator');

               foreach ($matches[1] as $name)
               {
                   $method = '_parameter'.ucfirst($name);

                   if (method_exists($this, $method))
                   {
                       $parameter = new ComActivitiesActivityParameter($name, $translator);
                       $this->$method($parameter);

                       $this->_parameters[$parameter->getName()] = $parameter;
                   }
               }
           }
       }

        return $this->_parameters;
    }

    /**
     * Casts an activity to a string.
     *
     * @return string The string representation of an activity
     */
    public function toString()
    {
        $format      = $this->getFormat();
        $parameters  = $this->getParameters();

        return $this->getObject('com:activities.activity.translator')->translate($format, $parameters);
    }

    /**
     * Actor activity Parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterActor(ComActivitiesActivityParameterInterface $parameter)
    {
        if ($this->findActor())
        {
            $parameter->link->href = $this->getActorUrl();
            $parameter->translate  = false;
            $value                 = $this->created_by_name;
        }
        else
        {
            $value = $this->created_by ? 'Deleted user' : 'Guest user';
        }

        $parameter->value = $value;
    }

    /**
     * Action activity parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterAction(ComActivitiesActivityParameterInterface $parameter)
    {
        $parameter->value = $this->status;
    }

    /**
     * Object activity parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterObject(ComActivitiesActivityParameterInterface $parameter)
    {
        $parameter->value = $this->name;

        $parameter->append(array(
            'attribs' => array('class' => array('object')),
        ));
    }

    /**
     * Title activity parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterTitle(ComActivitiesActivityParameterInterface $parameter)
    {
        $parameter->value     = $this->title;
        $parameter->translate = false;

        if ($this->findObject()) {
            $parameter->link->href = $this->getObjectUrl();
        }

        if ($this->status == 'deleted') {
            $parameter->attribs = array('class' => array('deleted'));
        }
    }

    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}

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
     * Stream objects.
     *
     * @var KObjectArray
     */
    protected $_objects;

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
            'format' => '{actor} {action} {object} {title}',
            'object_table' => $config->data->package . '_' . KStringInflector::pluralize($config->data->name),
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

    public function getMessageFormat()
    {
        return $this->_format;
    }

    public function findActor()
    {
        return (bool) $this->getObject('user.provider')->load($this->created_by)->getId();
    }

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

    public function findTarget()
    {
        return null; // Activities do not have targets by default.
    }

    public function getStreamObjects()
    {
        if (!$this->_objects)
        {
            $objects = $this->getObject('lib:object.array');

            foreach ($this->getMethods() as $method)
            {
                if (strstr($method, '_object'))
                {
                    $name   = strtolower(str_replace('_object', '', $method));
                    $object = new ComActivitiesActivityStreamObject($name);

                    $this->$method($object);

                    $objects[$name] = $object;
                }
            }

            $this->_objects = $objects;
        }

        return $this->_objects;
    }

    protected function _objectActor(ComActivitiesActivityStreamObjectInterface $object)
    {
        $object->objectType = 'user';
        $object->id         = $this->created_by;
        $object->url        = 'option=com_users&task=user.edit&id=' . $this->created_by;

        if (!$this->findActor())
        {
            $object->setDeleted(true);
        }
    }

    protected function _objectObject(ComActivitiesActivityStreamObjectInterface $object)
    {
        $object->id         = $this->row;
        $object->objectType = $this->name;
        $object->url        = 'option=com_' . $this->package . '&view=' . $this->name . '&id=' . $this->row;

        if (!$this->findObject())
        {
            $object->setDeleted(true);
        }
    }

    /**
     * Get the activity parameters
     *
     * @return array The activity parameters.
     */
    public function getMessageParameters()
    {
       if(!isset($this->_parameters))
       {
           $this->_parameters = array();

           if (preg_match_all('/\{(.*?)\}/', $this->getMessageFormat(), $matches) !== false)
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
     * Actor activity Parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterActor(ComActivitiesActivityParameterInterface $parameter)
    {
        $actor = $this->getStreamObjects()->actor;

        if (!$actor->isDeleted())
        {
            $parameter->link->href = $actor->url;
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

        $object = $this->getStreamObjects()->object;

        if (!$object->isDeleted()) {
            $parameter->link->href = $object->url;
        }

        if ($this->status == 'deleted') {
            $parameter->attribs = array('class' => array('deleted'));
        }
    }

    public function toString()
    {
        $format      = $this->getMessageFormat();
        $parameters  = $this->getMessageParameters();

        return $this->getObject('com:activities.activity.translator')->translate($format, $parameters);
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

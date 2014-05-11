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
     * The message translator.
     *
     * @var mixed
     */
    protected $_translator;

    /**
     * A list of required columns.
     *
     * @var array
     */
    protected $_required = array('package', 'name', 'action', 'title', 'status');

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_format     = $config->format;
        $this->_translator = $config->translator;
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
            'format'     => '{actor} {action} {object} {title}',
            'translator' => 'com:activities.activity.translator'
        ));

        parent::_initialize($config);
    }

    /**
     * Instantiate the object
     *
     * @param   KObjectConfigInterface $config      Configuration options
     * @param 	KObjectManagerInterface $manager	A KObjectManagerInterface object
     * @return  KObjectInterface
     */
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

    /**
     * Get the message format
     *
     * An activity message format is a compact representation of the activity which also provides information
     * about the parameters it may contain.
     *
     * @return string The activity message format.
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Check if the activity actor still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function hasActor()
    {
        return (bool) $this->getObject('user.provider')->load($this->created_by)->getId();
    }

    /**
     * Get the activity actor URL
     *
     * @return string|null The activity actor URL, null if not linkable or reachable.
     */
    public function getActorUrl()
    {
        $url = null;

        if($this->hasActor())
        {
            if ($this->created_by) {
                $url = 'option=com_users&task=user.edit&id=' . $this->created_by;
            }
        }

        return $url;
    }

    /**
     * Check if the activity object still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function hasObject()
    {
        return false;
    }

    /**
     * Get the activity object URL
     *
     * @return string|null The activity object URL, null if not linkable.
     */
    public function getObjectUrl()
    {
        $url = null;

        if($this->hasObject())
        {
            if ($this->package && $this->name && $this->row) {
                $url = 'option=com_' . $this->package . '&task=' . $this->name . '.edit&id=' . $this->row;
            }
        }

        return $url;
    }

    /**
     * Get the activity object type
     *
     * @return string The object type.
     */
    public function getObjectType()
    {
        return $this->name;
    }

    /**
     * Checks if the activity target still exists, i.e. it is still stored or reachable.
     *
     * @return boolean True if still exists, false otherwise.
     */
    public function hasTarget()
    {
        return false; // Activities don't have targets by default.
    }

    /**
     * Get the activity target identifier
     *
     * @return string|null The identifier of the target, null if no target.
     */
    public function getTargetId()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Get the activity target URL
     *
     * @return string|null The activity target URL, null if not linkable.
     */
    public function getTargetUrl()
    {
        return null; // Non-linkable as no target by default.
    }

    /**
     * Get the activity target type
     *
     * @return string|null The target type, null if no target.
     */
    public function getTargetType()
    {
        return null; // Activities don't have targets by default.
    }

    /**
     * Get the message parameters
     *
     * @return array The message parameters.
     */
    public function getActivityParameters()
    {
       if(!isset($this->_parameters))
       {
           $this->_parameters = array();

           if (preg_match_all('/\{(.*?)\}/', $this->getFormat(), $matches) !== false)
           {
               foreach ($matches[1] as $name)
               {
                   $method = '_parameter'.ucfirst($name);

                   if (method_exists($this, $method))
                   {
                       $parameter = new ComActivitiesActivityParameter($name);
                       $this->$method($parameter);

                       $this->_parameters[$parameter->getName()] = $parameter;
                   }
               }
           }
       }

        return $this->_parameters;
    }

    /**
     * Get the activity translator
     *
     * @return ComActivitiesActivityTranslatorInterface The message translator.
     */
    public function getTranslator()
    {
        if (!$this->_translator instanceof ComActivitiesActivityTranslatorInterface) {
            $this->_translator = $this->getObject($this->_translator);
        }

        return $this->_translator;
    }

    /**
     * Casts an activity message to string.
     *
     * @return string The string representation of an activity message.
     */
    public function toString()
    {
        return $this->getTranslator()->translateMessage($this);
    }

    /**
     * Actor Activity Parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterActor(ComActivitiesActivityParameterInterface $parameter)
    {
        if ($this->hasActor())
        {
            $parameter->url = $this->getActorUrl();
            $value  = $this->created_by_name;
        }
        else
        {
            $value = $this->created_by ? 'Deleted user' : 'Guest user';
            $parameter->translate = true;
        }

        $parameter->value = $value;
    }

    /**
     * Action Activity parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterAction(ComActivitiesActivityParameterInterface $parameter)
    {
        $parameter->value     = $this->status;
        $parameter->translate = true;
    }

    /**
     * Object Activity Parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterObject(ComActivitiesActivityParameterInterface $parameter)
    {
        $parameter->value     = $this->name;
        $parameter->translate = true;

        $parameter->append(array(
            'attributes' => array('class' => array('object')),
        ));
    }

    /**
     * Title Activity Parameter
     *
     * @param ComActivitiesActivityParameterInterface $parameter The activity parameter.
     * @return  void
     */
    protected function _parameterTitle(ComActivitiesActivityParameterInterface $parameter)
    {
        $parameter->value = $this->title;

        if (!$parameter->url && $this->hasObject() && ($url = $this->getObjectUrl())) {
            $parameter->url = $url;
        }

        if ($this->status == 'deleted') {
            $parameter->attributes = array('class' => array('deleted'));
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

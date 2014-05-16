<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Loggable Controller Behavior
 *
 * This behavior will delegate controller action logging to one or more loggers.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerBehaviorLoggable extends KControllerBehaviorAbstract
{
    /**
     * List of loggers
     *
     * @var array
     */
    protected $_loggers = array();

    /**
     * Logger queue
     *
     * @var	KObjectQueue
     */
    protected $_queue;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the logger queue
        $this->_queue = $this->getObject('lib:object.queue');

        //Attach the loggers
        $loggers = KObjectConfig::unbox($config->loggers);

        foreach ($loggers as $key => $value)
        {
            if (is_numeric($key)) {
                $this->attachLogger($value);
            } else {
                $this->attachLogger($key, $value);
            }
        }
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
            'priority' => self::PRIORITY_LOWEST,
            'loggers'  => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    final public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $action = $command->getName();

        foreach($this->_queue as $logger)
        {
            if (in_array($action, $logger->getActions()))
            {
                $object = $logger->getActivityObject($command);

                if ($object instanceof KModelEntityInterface)
                {
                    $subject = $logger->getActivitySubject($command);
                    $logger->log($action, $object, $subject);
                }
            }
        }
    }

    /**
     * Attach a logger
     *
     * @param   mixed  $logger An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param   array $config  An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return ComActivitiesControllerBehaviorLoggable
     */
    public function attachLogger($logger, $config = array())
    {
        $identifier = $this->getIdentifier($logger);

        if (!in_array((string) $identifier, $this->_loggers))
        {
            $logger = $this->getObject($identifier, $config);

            if (!($logger instanceof ComActivitiesActivityLoggerInterface))
            {
                throw new UnexpectedValueException(
                    "Logger $identifier does not implement ComActivitiesActivityLoggerInterface"
                );
            }

            $this->_queue->enqueue($logger, self::PRIORITY_NORMAL);
            $this->_loggers[] = $identifier;
        }

        return $this;
    }

    /**
     * Get the behavior name
     *
     * Hardcode the name to 'loggable'.
     *
     * @return string
     */
    public function getName()
    {
        return 'loggable';
    }

    /**
     * Get an object handle
     *
     * Force the object to be enqueue in the command chain.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}

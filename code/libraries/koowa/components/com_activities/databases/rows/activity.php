<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Database Row Class
 *
 * @author         Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package        Nooku_Components
 * @subpackage     Activities
 */
class ComActivitiesDatabaseRowActivity extends KDatabaseRowDefault
{
    /**
     * @var ComActivitiesDatabaseRowActivityStrategyInterface The activity row strategy.
     */
    protected $_strategy;

    /**
     * @var array A list of required columns.
     */
    protected $_required = array('package', 'name', 'action', 'title', 'status');

    public function save()
    {
        if (!in_array($this->application, array('admin', 'site')))
        {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage('Invalid application value');
            return false;
        }

        if (!in_array($this->type, array('com')))
        {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage('Invalid type value');
            return false;
        }

        if (!$this->status)
        {
            // Attempt to provide a default status.
            switch ($this->action)
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

            if ($status)
            {
                $this->status = $status;
            }
        }

        foreach ($this->_required as $column)
        {
            if (empty($this->$column))
            {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage('Missing required data');
                return false;
            }
        }

        if ($this->isModified('metadata') && !is_null($this->metadata))
        {
            // Encode meta data.
            $metadata = json_encode($this->metadata);
            if ($metadata === false)
            {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage('Unable to encode meta data');
                return false;
            }
            $this->metadata = $metadata;
        }

        $package = $this->package;

        $result = parent::save();

        // Reset strategy if package changed.
        if ($result && ($package != $this->package))
        {
            $this->_strategy = null;
        }

        return $result;
    }

    public function __set($column, $value)
    {
        // Reset strategy on package change.
        if ($column == 'package' && $this->package != $value) $this->_strategy = null;
        return parent::__set($column, $value);
    }

    public function __get($key)
    {
        $value = parent::__get($key);

        if ($key == 'metadata' && is_string($value))
        {
            // Try to decode it.
            $metadata = json_decode($value);
            if ($metadata !== null)
            {
                $value = $metadata;
            }
        }

        return $value;
    }

    /**
     * Strategy setter.
     *
     * @param ComActivitiesDatabaseRowActivityStrategyInterface $strategy The row strategy.
     *
     * @return $this
     */
    public function setStrategy(ComActivitiesDatabaseRowActivityStrategyInterface $strategy)
    {
        $strategy->setRow($this);
        $this->_strategy = $strategy;
        return $this;
    }

    /**
     * Strategy getter.
     *
     * @return ComActivitiesDatabaseRowActivityStrategyInterface The row strategy.
     */
    public function getStrategy()
    {
        if (!$this->_strategy instanceof ComActivitiesDatabaseRowActivityStrategyInterface)
        {
            $strategy       = clone $this->getIdentifier();
            $strategy->path = array('database', 'row', 'activity', 'strategy');
            $strategy->name = $this->package ? $this->package : 'default';

            $this->setStrategy($this->getService($strategy, array('row' => $this)));
        }

        return $this->_strategy;
    }

    /**
     * @see ComActivitiesDatabaseRowActivity::reset().
     *
     * Overloaded for strategy reset.
     */
    public function reset()
    {
        $result = parent::reset();

        if ($result)
        {
            $this->_strategy = null;
        }

        return $result;
    }

    /**
     * @see ComActivitiesDatabaseRowActivity::load().
     *
     * Overloaded for strategy reset.
     */
    public function load()
    {
        $package = $this->package;

        $result = parent::load();

        if ($result && ($package != $this->package))
        {
            $this->_strategy = null;
        }

        return $result;
    }

    /**
     * @see ComActivitiesDatabaseRowActivity::delete().
     *
     * Overloaded for strategy reset.
     */
    public function delete()
    {
        $result = parent::delete();

        if ($result)
        {
            $this->_strategy = null;
        }

        return $result;
    }

    /**
     * @see KDatabaseRowTable::__call()
     *
     * Overloaded for calling strategy methods.
     */
    public function __call($method, $arguments)
    {
        $strategy = $this->getStrategy();

        if (method_exists($strategy, $method) && is_callable(array($strategy, $method)))
        {
            $result = call_user_func_array(array($strategy, $method), $arguments);
        }
        else
        {
            $result = parent::__call($method, $arguments);
        }

        return $result;
    }
}

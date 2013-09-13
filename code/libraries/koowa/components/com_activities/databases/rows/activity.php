<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Database Row
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesDatabaseRowActivity extends KDatabaseRowDefault implements ComActivitiesDatabaseRowActivityInterface
{
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

        return parent::save();
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
     * Strategy getter.
     *
     * @return ComActivitiesDatabaseRowActivityStrategyInterface The row strategy.
     */
    public function getStrategy()
    {
        $strategy       = clone $this->getIdentifier();
        $strategy->path = array('database', 'row', 'activity', 'strategy');
        $strategy->name = $this->isNew() ? 'new' : $this->package;

        return $this->getObject($strategy, array('row' => $this));
    }

    /**
     * @see ComActivitiesDatabaseRowActivityInterface::toString()
     */
    public function toString($html = true)
    {
        // Delegate task to strategy.
        return $this->getStrategy()->toString($html);
    }

    /**
     * @see ComActivitiesDatabaseRowActivityInterface::getStreamData()
     */
    public function getStreamData()
    {
        // Delegate task to strategy.
        return $this->getStrategy()->getStreamData();
    }
}

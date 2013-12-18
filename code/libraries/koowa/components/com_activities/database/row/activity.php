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
class ComActivitiesDatabaseRowActivity extends KDatabaseRowTable implements ComActivitiesDatabaseRowActivityInterface
{
    /**
     * @var array A list of required columns.
     */
    protected $_required = array('package', 'name', 'action', 'title', 'status');

    public function save()
    {
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

        if ($this->isModified('metadata') && !is_null($this->metadata))
        {
            // Encode meta data.
            $metadata = json_encode($this->metadata);

            if ($metadata === false)
            {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage($translator->translate('Unable to encode meta data'));
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
            if ($metadata !== null) {
                $value = $metadata;
            }
        }

        return $value;
    }

    public function getStrategy()
    {
        $strategy = null;

        if (!$this->isNew() && !$this->getModified())
        {
            $identifier       = clone $this->getIdentifier();
            $identifier->path = array('database', 'row', 'activity', 'strategy');
            $identifier->name = $this->package;

            if (!file_exists($identifier->getLocator()->findPath($identifier)))
            {
                // Manually fallback to default.
                $identifier->path = array('database', 'row', 'activity');
                $identifier->name = 'strategy';
            }

            $strategy = $this->getObject($identifier, array('row' => $this));
        }

        return $strategy;
    }
}

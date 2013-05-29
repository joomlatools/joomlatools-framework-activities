<?php
/**
 * @version        $Id$
 * @package        Nooku_Components
 * @subpackage     Activities
 * @copyright      Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
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
    protected $_required = array('package', 'name', 'action', 'title', 'status');

    public function save()
    {

        if (!in_array($this->application, array('admin', 'site'))) {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage('Invalid application value');
            return false;
        }

        if (!in_array($this->type, array('com'))) {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage('Invalid type value');
            return false;
        }

        if (!$this->status) {
            // Attempt to provide a default status.
            switch ($this->action) {
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

        foreach ($this->_required as $column) {
            if (empty($this->$column)) {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage('Missing required data');
                return false;
            }
        }

        if ($this->isModified('metadata') && !is_null($this->metadata)) {
            // Encode meta data.
            $metadata = json_encode($this->metadata);
            if ($metadata === false) {
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

        if ($key == 'metadata' && is_string($value)) {
            // Try to decode it.
            $metadata = json_decode($value);
            if ($metadata !== null) {
                $value = $metadata;
            }
        }

        return $value;
    }
}
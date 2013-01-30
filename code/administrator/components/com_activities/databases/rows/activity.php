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
    public function save()
    {

        if (!in_array($this->application, $applications = array('admin', 'site'))) {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage(JText::sprintf('COM_ACTIVITIES_INVALID_APP', implode(', ', $applications)));
            return false;
        }

        if (!in_array($this->type, $types = array('com'))) {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage(JText::sprintf('COM_ACTIVITIES_INVALID_TYPE', implode(', ', $types)));
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

        $required_columns = array('package', 'name', 'action', 'title', 'status');
        $empty_columns    = array();

        foreach ($required_columns as $column) {
            if (empty($this->$column)) {
                $empty_columns[] = $column;
            }
        }

        if (count($empty_columns)) {
            $this->setStatus(KDatabase::STATUS_FAILED);
            $this->setStatusMessage(JText::sprintf('COM_ACTIVITIES_REQUIRED_COLUMNS', implode(', ', $empty_columns)));
            return false;
        }

        if ($this->isModified('meta') && !is_null($this->meta)) {
            // Encode metadata.
            $meta = json_encode($this->meta);
            if ($meta === false) {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage(JText::_('COM_ACTIVITIES_META_ENCODE_FAILED'));
                return false;
            }
            $this->meta = $meta;
        }

        return parent::save();
    }

    public function __get($key)
    {
        $value = parent::__get($key);

        if ($key == 'meta' && is_string($value)) {
            // Try to decode it.
            $meta = json_decode($value);
            if ($meta !== null) {
                $value = $meta;
            }
        }

        return $value;
    }
}
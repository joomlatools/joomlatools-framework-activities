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

        $required_columns = array('package','name','action','title', 'status');
        $empty_columns = array();

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

        $result = parent::save();

        if ($result && ($this->action == 'delete') && $this->row) {
            $activities = $this->getService('com://admin/activities.model.activities')->type($this->com)
                ->package($this->package)->name($this->name)->row($this->row)->getList();
            if (count($activities)) {
                // Set resource activities as no longer in database.
                $activities->setColumn('indb', 0);
                $activities->save();
            }
        }

        return $result;
    }
}
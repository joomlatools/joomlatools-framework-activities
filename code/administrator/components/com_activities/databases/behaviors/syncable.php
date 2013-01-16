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
 * Syncable Database Behavior Class
 *
 * @author         Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package        Nooku_Components
 * @subpackage     Activities
 */
class ComActivitiesDatabaseBehaviorSyncable extends KDatabaseBehaviorAbstract
{
    protected function _afterTableInsert(KCommandContext $context)
    {
        $data = $context->data;

        if (($data->action == 'delete') && $data->row) {
            $data->getTable()->sync(array('package' => $data->package, 'row' => $data->row, 'name' => $data->name));
        }
    }
}
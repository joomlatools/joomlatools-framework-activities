<?php
/**
 * @version     $Id: executable.php 1485 2012-02-10 12:32:02Z johanjanssens $
 * @package     Nooku_Server
 * @subpackage  Activities
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Activity Controller
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Activities
 */
class ComActivitiesControllerActivity extends ComDefaultControllerDefault
{
    protected function _actionPurge(KCommandContext $context)
    {
        $db = $this->getModel()->getTable()->getDatabase();
        
        $query = $this->getModel()->getListQuery();
        $query->columns = array();
        
        // MySQL doesn't allow limit or order in multi table deletes
        $query->limit = null;
        $query->order = null;
        
        $query = 'DELETE `tbl` ' .$query;
        
        if (!$db->execute($query)) {
            $context->setError(new KControllerException(
                'Delete Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
            ));
        } else {
            $context->status = KHttpResponse::NO_CONTENT;
        }
    }
}
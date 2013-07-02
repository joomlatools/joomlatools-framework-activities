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
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // TODO To be removed as soon as the problem with language files loading on HMVC calls is solved
        JFactory::getLanguage()->load('com_activities', JPATH_ADMINISTRATOR);

        $this->registerCallback('before.add', array($this, 'setIp'));
    }

    protected function _actionPurge(KCommandContext $context)
    {
        if (!$this->getModel()->getTable()->getDatabase()->execute($this->getModel()->getPurgeQuery()))
        {
            $context->setError(new KControllerException(
                'Delete Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
            ));
        }
        else
        {
            $context->status = KHttpResponse::NO_CONTENT;
        }
    }

    public function setIp(KCommandContext $context)
    {
        $context->data->ip = KRequest::get('server.REMOTE_ADDR', 'ip');
    }
}
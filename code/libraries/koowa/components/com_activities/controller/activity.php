<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Controller
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerActivity extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getObject('translator')->loadTranslations('com_activities');

        $this->registerCallback('before.add', array($this, 'setIp'));
    }

    protected function _actionPurge(KControllerContextInterface $context)
    {
        if (!$this->getModel()->getTable()->getDatabase()->execute($this->getModel()->getPurgeQuery()))
        {
            $context->setError(new KControllerExceptionActionFailed(
                'Delete Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
            ));
        }
        else $context->status = KHttpResponse::NO_CONTENT;
    }

    public function setIp(KControllerContextInterface $context)
    {
        $context->request->data->ip = KRequest::get('server.REMOTE_ADDR', 'ip');
    }
}

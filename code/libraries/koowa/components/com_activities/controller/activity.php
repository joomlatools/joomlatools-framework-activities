<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-activities for the canonical source repository
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

        $this->addCommandCallback('before.add', '_setIp');
    }

    protected function _actionPurge(KControllerContextInterface $context)
    {
        $model = $this->getModel();
        $state = $model->getState();
        $query = $this->getObject('lib:database.query.delete');

        $query->table(array($model->getTable()->getName()));

        if ($state->end_date && $state->end_date != '0000-00-00')
        {
            $end_date = $this->getObject('lib:date', array('date' => $state->end_date));
            $end      = $end_date->format('Y-m-d');

            $query->where('DATE(created_on) <= :end')->bind(array('end' => $end));
        }

        if (!$this->getModel()->getTable()->getAdapter()->execute($query)) {
            throw new KControllerExceptionActionFailed('Delete Action Failed');
        } else {
            $context->status = KHttpResponse::NO_CONTENT;
        }
    }

    protected function _setIp(KControllerContextInterface $context)
    {
        $context->request->data->ip = $this->getObject('request')->getAddress();
    }
}

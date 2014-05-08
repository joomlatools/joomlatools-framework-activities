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

        $this->addCommandCallback('before.add', '_setIp');
        $this->addCommandCallback('before.add', '_setEntityPackage');
    }

    protected function _actionPurge(KControllerContextInterface $context)
    {
        if (!$this->getModel()->getTable()->getAdapter()->execute($this->getModel()->getPurgeQuery())) {
            throw new KControllerExceptionActionFailed('Delete Action Failed');
        } else {
            $context->status = KHttpResponse::NO_CONTENT;
        }
    }

    protected function _setIp(KControllerContextInterface $context)
    {
        $context->request->data->ip = $this->getObject('request')->getAddress();
    }

    /**
     * Entity package setter.
     *
     * Sets the model state for creating entities of a type given by the activity package.
     *
     * @param KControllerContextInterface $context
     */
    protected function _setEntityPackage(KControllerContextInterface $context)
    {
        if ($data = $context->request->data) {
            $this->getModel()->setActivityData($data);
        }
    }
}

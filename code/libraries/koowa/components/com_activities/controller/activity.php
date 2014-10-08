<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Controller.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesControllerActivity extends KControllerModel
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getObject('translator')->load('com:activities');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('purgeable')
        ));

        parent::_initialize($config);
    }

    /**
     * Set the ip address if we are adding a new activity.
     *
     * @param KControllerContextInterface $context A command context object.
     *
     * @return KModelEntityInterface
     */
    protected function _beforeAdd(KControllerContextInterface $context)
    {
        $context->request->data->ip = $this->getObject('request')->getAddress();
    }
}

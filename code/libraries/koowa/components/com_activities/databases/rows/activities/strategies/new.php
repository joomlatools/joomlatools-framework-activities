<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComActivitiesDatabaseRowActivityStrategyNew implements ComActivitiesDatabaseRowActivityStrategyInterface
{
    /**
     * @var ComActivitiesDatabaseRowActivity The activity row object.
     */
    protected $_row;

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::setRow()
     */
    public function setRow(ComActivitiesDatabaseRowActivity $row)
    {
        $this->_row = $row;
        return $this;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getRow()
     */
    public function getRow()
    {
        return $this->_row;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::toString()
     */
    public function toString($html = true)
    {
        return '';
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::actorExists()
     */
    public function actorExists()
    {
        return false;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::objectExists()
     */
    public function objectExists()
    {
        return false;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::targetExists()
     */
    public function targetExists()
    {
        return false;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getActorUrl()
     */
    public function getActorUrl()
    {
        return null;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getObjectUrl()
     */
    public function getObjectUrl()
    {
        return null;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getTargetUrl()
     */
    public function getTargetUrl()
    {
        return null;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::hasTarget()
     */
    public function hasTarget()
    {
        return false;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::toStream()
     */
    public function getStreamData()
    {
        return array();
    }
}
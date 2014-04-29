<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Parameter Set.
 */
class ComActivitiesMessageParameterSet extends KObjectSet implements ComActivitiesMessageParameterSetInterface
{
    public function getContent()
    {
        $text = array();

        foreach ($this as $parameter) {
            $text[$parameter->getLabel()] = $parameter->getContent();
        }

        return $text;
    }

    public function insert(KObjectHandlable $parameter)
    {
        if (!$parameter instanceof ComActivitiesMessageParameterInterface) {
            throw new InvalidArgumentException('Parameter must be of ComActivitiesMessageParameterInterface type');
        }

        return parent::insert($parameter);
    }

    public function remove(KObjectHandlable $parameter)
    {
        if (!$parameter instanceof ComActivitiesMessageParameterInterface) {
            throw new InvalidArgumentException('Parameter must be of ComActivitiesMessageParameterInterface type');
        }

        return parent::remove($parameter);
    }

    public function setData(array $data)
    {
        foreach ($data as $parameter) {
            $this->insert($parameter);
        }

        return $this;
    }
}
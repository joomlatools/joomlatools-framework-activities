<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Variable Set Class.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesMessageVariableSet extends KObjectSet implements ComActivitiesMessageVariableSetInterface
{
    public function getContent()
    {
        $text = array();

        foreach ($this as $variable) {
            $text[$variable->getLabel()] = $variable->getContent();
        }

        return $text;
    }

    public function insert(KObjectHandlable $variable)
    {
        if (!$variable instanceof ComActivitiesMessageVariableInterface) {
            throw new InvalidArgumentException('Variable must be of ComActivitiesMessageVariableInterface type');
        }

        return parent::insert($variable);
    }

    public function remove(KObjectHandlable $variable)
    {
        if (!$variable instanceof ComActivitiesMessageVariableInterface) {
            throw new InvalidArgumentException('Variable must be of ComActivitiesMessageVariableInterface type');
        }

        return parent::remove($variable);
    }

    public function setData(array $data)
    {
        foreach ($data as $parameter) {
            $this->insert($parameter);
        }

        return $this;
    }
}
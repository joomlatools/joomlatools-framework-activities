<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Parameters Class
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesMessageParameters extends KObjectSet implements ComActivitiesMessageParametersInterface
{
    /**
     * Returns the set content.
     *
     * @return array Associative array containing parameter label and content pairs.
     */
    public function getContent()
    {
        $text = array();

        foreach ($this as $parameter) {
            $text[$parameter->getLabel()] = $parameter->getContent();
        }

        return $text;
    }

    /**
     * Set the parameters
     *
     * @param array $data An array containing parameters objects.
     * @return ComActivitiesMessageParametersInterface
     */
    public function setData(array $data)
    {
        foreach ($data as $parameter) {
            $this->insert($parameter);
        }

        return $this;
    }

    /**
     * Insert a parameter
     *
     * @param ComActivitiesMessageParameterInterface $parameter The parameter object to be inserted.
     * @throws InvalidArgumentException
     * @return boolean    TRUE on success FALSE on failure
     */
    public function insert(KObjectHandlable $parameter)
    {
        if (!$parameter instanceof ComActivitiesMessageParameterInterface) {
            throw new InvalidArgumentException('Parameter must implement ComActivitiesMessageParameterInterface');
        }

        return parent::insert($parameter);
    }

    /**
     * Removes a parameter
     *
     * @param ComActivitiesMessageParameterInterface $parameter The parameter object to be removed.
     * @throws InvalidArgumentException
     * @return ComActivitiesMessageParametersInterface
     */
    public function remove(KObjectHandlable $parameter)
    {
        if (!$parameter instanceof ComActivitiesMessageParameterInterface) {
            throw new InvalidArgumentException('Parameter must implement ComActivitiesMessageParameterInterface');
        }

        return parent::remove($parameter);
    }
}
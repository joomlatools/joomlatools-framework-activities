<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Parameter Set Interface.
 */
interface ComActivitiesMessageParameterSetInterface extends Iterator, ArrayAccess, Countable, Serializable
{
    /**
     * Returns the set content.
     *
     * @return array Associative array containing parameter label and content pairs.
     */
    public function getContent();

    /**
     * Insert a parameter into the set.
     *
     * @param ComActivitiesMessageParameterInterface $parameter The parameter object to be inserted.
     * @throws InvalidArgumentException
     *
     * @return boolean    TRUE on success FALSE on failure
     */
    public function insert(KObjectHandlable $parameter);

    /**
     * Removes a parameter from the set.
     *
     * @param ComActivitiesMessageParameterInterface $parameter The parameter object to be removed.
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function extract(KObjectHandlable $parameter);

    /**
     * Parameters setter.
     *
     * @param array $data An array containing parameter objects.
     *
     * @return $this
     */
    public function setData(array $data);
}
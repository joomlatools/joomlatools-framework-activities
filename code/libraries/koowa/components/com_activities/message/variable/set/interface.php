<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Message Variable Set Interface.
 */
interface ComActivitiesMessageVariableSetInterface extends IteratorAggregate, ArrayAccess, Countable, Serializable
{
    /**
     * Returns the set content.
     *
     * @return array Associative array containing variable label and content pairs.
     */
    public function getContent();

    /**
     * Insert a variable into the set.
     *
     * @param ComActivitiesMessageVariableInterface $variable The variable object to be inserted.
     * @throws InvalidArgumentException
     *
     * @return boolean    TRUE on success FALSE on failure
     */
    public function insert(KObjectHandlable $variable);

    /**
     * Removes a variable from the set.
     *
     * @param ComActivitiesMessageVariableInterface $variable The variable object to be removed.
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function remove(KObjectHandlable $variable);

    /**
     * Variables setter.
     *
     * @param array $data An array containing variable objects.
     *
     * @return $this
     */
    public function setData(array $data);
}
<?php
/**
 * @package        Nooku_Components
 * @subpackage     Activities
 * @copyright      Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */

/**
 * Activities JSON View Class
 *
 * @author         Israel Canasa <http://nooku.assembla.com/profile/israelcanasa>
 * @category       Nooku
 * @package        Nooku_Components
 * @subpackage     Activities
 * @see            http://activitystrea.ms/specs/json/1.0/
 */

class ComActivitiesViewActivitiesJson extends KViewJson
{
    protected function _getItem(KDatabaseRowInterface $row)
    {
        if ($this->getLayout() == 'stream')
        {
            $item = $row->getStreamData();
        }
        else
        {
            $item = parent::_getItem($row);
        }

        return $item;
    }

    protected function _getList(KDatabaseRowsetInterface $rowset)
    {
        $result = array();

        foreach ($rowset as $row)
        {
            $result[] = $this->_getItem($row);
        }

        return $result;
    }
}

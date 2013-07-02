<?php
/**
 * @version        $Id: json.php 1485 2012-02-10 12:32:02Z johanjanssens $
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
    /**
     * Get the list data
     *
     * @return array The array with data to be encoded to json
     */
    protected function _getList()
    {
        if ($this->getLayout() == 'stream') {
            $list = $this->_getStream();
        } else {
            $list = parent::_getList();
        }
        return $list;
    }

    /**
     * Get the stream data
     *
     * @return array The array with data to be encoded to json
     */
    protected function _getStream()
    {
        //Get the model
        $model = $this->getModel();

        $url = clone KRequest::url();

        //Get the model state
        $state = $model->getState();

        $vars = array();
        foreach ($state->toArray(false) as $var) {
            if (!$var->unique) {
                $vars[] = $var->name;
            }
        }

        $data = array(
            'version' => '1.0',
            'href'    => (string) $url->setQuery(array_merge($url->getQuery(true), $state->toArray())),
            'url'     => array(
                'type'     => 'application/json',
                'template' => (string) $url->toString(KHttpUrl::BASE) . '?{&' . implode(',', $vars) . '}',
            ),
            'offset'  => (int) $state->offset,
            'limit'   => (int) $state->limit,
            'total'   => 0,
            'items'   => array()
        );

        if ($list = $model->getList()) {
            $items = array();
            foreach ($list as $item) {
                $items[] = $this->_toStream($item);
            }

            $data = array_merge($data, array(
                'total' => $model->getTotal(),
                'items' => $items
            ));
        }

        return $data;
    }

    /**
     * Casts a row object into an activity stream entry.
     *
     * @param KDatabaseRowDefault $item
     *
     * @return array
     */
    protected function _toStream(KDatabaseRowDefault $item)
    {
        $id = array(
            'tag:' . KRequest::get('server.HTTP_HOST', 'string'),
            'id:' . $item->id
        );

        $base = KRequest::url()
            ->get(KHttpUrl::BASE);

        return array(
            'id'        => implode(',', $id),
            'published' => $this->getService('com://admin/activities.template.helper.date')->format(array(
                'date'   => $item->created_on,
                'format' => '%Y-%m-%dT%TZ'
            )),
            'verb'      => $item->action,
            'object'    => array(
                'url' => $this->getService('koowa:http.url', array('url' => $base))->setQuery(array(
                    'option' => $item->type . '_' . $item->package,
                    'view'   => $item->name,
                    'id'     => $item->row,
                ))->get()
            ),
            'target'    => array(
                'url' => $this->getService('koowa:http.url', array('url' => $base))->setQuery(array(
                    'option' => $item->type . '_' . $item->package,
                    'view'   => $item->name,
                ))->get()
            ),
            'actor'     => array(
                'url' => $this->getService('koowa:http.url', array('url' => $base))->setQuery(array(
                    'option' => 'com_users',
                    'view'   => 'user',
                    'id'     => $item->created_by
                ))->get()
            )
        );
    }
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activities Json View
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 * @see     http://activitystrea.ms/specs/json/1.0/
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
            ->toString(KHttpUrl::BASE);

        return array(
            'id'        => implode(',', $id),
            'published' => $this->getObject('com://admin/activities.template.helper.date')->format(array(
                'date'   => $item->created_on,
                'format' => 'C'
            )),
            'verb'      => $item->action,
            'object'    => array(
                'url' => $this->getObject('koowa:http.url', array('url' => $base))->setQuery(array(
                    'option' => $item->type . '_' . $item->package,
                    'view'   => $item->name,
                    'id'     => $item->row,
                ))->toString()
            ),
            'target'    => array(
                'url' => $this->getObject('koowa:http.url', array('url' => $base))->setQuery(array(
                    'option' => $item->type . '_' . $item->package,
                    'view'   => $item->name,
                ))->toString()
            ),
            'actor'     => array(
                'url' => $this->getObject('koowa:http.url', array('url' => $base))->setQuery(array(
                    'option' => 'com_users',
                    'view'   => 'user',
                    'id'     => $item->created_by
                ))->toString()
            )
        );
    }
}

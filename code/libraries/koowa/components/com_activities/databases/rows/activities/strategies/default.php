<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComActivitiesDatabaseRowActivityStrategyDefault extends ComActivitiesDatabaseRowActivityStrategyAbstract
{
    /**
     * @see ComActivitiesDatabaseRowActivityStrategyAbstract::_getString()
     */
    protected function _getString()
    {
        return '{actor} {action} {object} {title}';
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyAbstract::_getIcon()
     */
    protected function _getIcon()
    {
        $classes = array('add' => 'icon-plus-sign', 'edit' => 'icon-edit', 'delete' => 'icon-trash');

        // Default.
        $icon = 'icon-task';

        $action = $this->action;

        if (in_array($action, array_keys($classes)))
        {
            $icon = $classes[$action];
        }

        return $icon;
    }

    protected function _setActor(KConfig $config)
    {
        if ($this->actorExists())
        {
            $config->link = array('url' => $this->getActorUrl());
            $text         = $this->created_by_name;
        }
        else
        {
            $text              = $this->created_by ? 'Deleted user' : 'Guest user';
            $config->translate = true;
        }

        $config->text = $text;
    }


    protected function _setAction(KConfig $config)
    {
        $config->append(array(
            'text'      => $this->status,
            'translate' => true));
    }


    protected function _setObject(KConfig $config)
    {
        $config->append(array(
            'translate'  => true,
            'text'       => $this->name,
            'attributes' => array('class' => array('object')),
        ));
    }

    protected function _setTitle(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(),
            'translate'  => false,
            'text'       => $this->title
        ));

        $link = $config->link;

        if (!$link->url && $this->objectExists() && ($url = $this->getObjectUrl()))
        {
            $link->url = $url;
        }

        if ($this->status == 'deleted')
        {
            $config->attributes = array('class' => array('deleted'));
        }
    }

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
        $string = $this->_getString();

        $translator = $this->getTranslator();

        $components = $translator->parse($string);

        $parameters = array();

        foreach ($components['parameters'] as $parameter)
        {
            $method = '_set' . ucfirst($parameter);

            if (method_exists($this, $method))
            {
                $config = new KConfig(array('link' => array()));

                call_user_func(array($this, $method), $config);

                $config->html            = $html;
                $config->label           = $parameter;
                $config->url             = $config->link->url;
                $config->link_attributes = $config->link->attributes;

                // Cleanup config object.
                unset($config->link);

                $parameters[] = $this->getService($this->_parameter, $config->toArray());
            }
        }

        $string = $translator->translate($string, $parameters);

        if ($html)
        {
            $string = '<i class="' . $this->_getIcon() . '" ></i >&nbsp;' . $string;
        }

        return $string;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::actorExists()
     */
    public function actorExists()
    {
        return $this->_resourceExists(array('table' => 'users', 'column' => 'id', 'value' => $this->created_by));
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::objectExists()
     */
    public function objectExists()
    {
        return $this->_resourceExists();
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::targetExists()
     */
    public function targetExists()
    {
        // Activities don't have targets by default.
        return false;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getActorUrl()
     */
    public function getActorUrl()
    {
        $url = null;

        if ($this->created_by)
        {
            $url = $this->_getUrl(array('url' => 'index.php?option=com_users&task=user.edit&id=' . $this->created_by));
        }

        return $url;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getObjectUrl()
     */
    public function getObjectUrl()
    {
        $url = null;

        if ($this->package && $this->name && $this->row)
        {
            $url = $this->_getUrl(array('url' => 'index.php?option=com_' . $this->package . '&task=' . $this->name . '.edit&id=' . $this->row));
        }

        return $url;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getTargetUrl()
     */
    public function getTargetUrl()
    {
        // Non-linkable as no target by default.
        return null;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::hasTarget()
     */
    public function hasTarget()
    {
        // Activities don't have targets by default.
        return false;
    }

    /**
     * @see ComActivitiesDatabaseRowActivityStrategyInterface::getStreamData()
     */
    public function getStreamData()
    {
        $tag = 'tag:' . KRequest::get('server.HTTP_HOST', 'string');

        $data = array(
            'id'        => $tag . ',id:' . $this->uuid,
            'title'     => $this->toString(false),
            'published' => $this->getService('com://admin/koowa.template.helper.date')->format(array(
                'date'   => $this->created_on,
                'format' => 'c'
            )),
            'verb'      => $this->action,
            'object'    => array(
                'id'         => $tag . ',id:' . $this->row,
                'objectType' => $this->name),
            'actor'     => array(
                'id'          => $this->created_by,
                'objectType'  => 'user',
                'displayName' => $this->created_by_name));

        if ($this->objectExists())
        {
            $data['object']['url'] = $this->getObjectUrl();
        }

        if ($this->actorExists())
        {
            $data['actor']['url'] = $this->getActorUrl();
        }

        return $data;
    }
}
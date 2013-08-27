<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Abstract Activity Database Row Strategy
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
abstract class ComActivitiesDatabaseRowActivityStrategyAbstract extends KObject implements ComActivitiesDatabaseRowActivityStrategyInterface
{
    /**
     * @var mixed The translator parameter identifier to instantiate.
     */
    protected $_parameter;

    /**
     * @var mixed The activity translator.
     */
    protected $_translator;

    /**
     * @var ComActivitiesDatabaseRowActivity The activity row object.
     */
    protected $_row;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!$config->row instanceof ComActivitiesDatabaseRowActivity)
        {
            throw new BadMethodCallException('The activity database row object is missing.');
        }

        $this->setRow($config->row);

        $this->_parameter  = $config->parameter;
        $this->_translator = $config->translator;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'parameter'  => 'com://admin/activities.activity.translator.parameter.default',
            'translator' => 'com://admin/activities.activity.translator.default',
        ));
        parent::_initialize($config);
    }

    /**
     * Activity icon getter.
     *
     * @return string The activity icon class value.
     */
    abstract protected function _getIcon();

    /**
     * Activity string getter.
     *
     * An activity string is a compact representation of the activity text which also provides information
     * about the variables it may contain. These are used in the same way Joomla! translation keys are
     * used for translating text to other languages.
     *
     * @return string The activity string.
     */
    abstract protected function _getString();

    /**
     * URL getter.
     *
     * @param array $config An optional configuration array.
     *
     * @return string The URL.
     */
    protected function _getUrl($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('route' => true, 'absolute' => true, 'url' => KRequest::url()));

        $url = $config->url;

        if ($config->route)
        {
            $url = JRoute::_($config->url, false);
        }
        else
        {
            // If routing is disabled, URLs are assumed to be relative to site root.
            $url = KRequest::root() . '/' . $url;
        }


        if ($config->absolute)
        {
            $url = KRequest::url()->toString(KHttpUrl::AUTHORITY) . $url;
        }

        return $url;
    }

    /**
     * Determines if a given resource exists.
     *
     * @param array $config An optional configuration array.
     *
     * @return bool True if it exists, false otherwise.
     */
    protected function _resourceExists($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'table'  => $this->package . '_' . KStringInflector::pluralize($this->name),
            'column' => $this->package . '_' . $this->name . '_' . 'id',
            'value'  => $this->row));

        $db = $this->getRow()->getTable()->getAdapter();

        $query = $this->getObject('koowa:database.query.select');
        $query->columns('COUNT(*)')->table($config->table)->where($config->column . ' = :value')
        ->bind(array('value' => $config->value));

        // Need to catch exceptions here as table may not longer exist.
        try
        {
            $result = $db->select($query, KDatabase::FETCH_FIELD);
        } catch (Exception $e)
        {
            $result = 0;
        }

        return (bool) $result;
    }

    /**
     * Translator setter.
     *
     * @param ComActivitiesActivityTranslatorInterface $translator The activity translator.
     *
     * @return $this
     */
    public function setTranslator(ComActivitiesActivityTranslatorInterface $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Translator getter.
     *
     * @return ComActivitiesActivityTranslatorInterface The activity translator.
     */
    public function getTranslator()
    {
        if (!$this->_translator instanceof ComActivitiesActivityTranslatorInterface)
        {
            $this->_translator = $this->getObject($this->_translator);
        }

        return $this->_translator;
    }

    /**
     * Returns activity row column values if a matching column for the requested key is found.
     *
     * @param string $key The requested key.
     *
     * @return mixed The row column value if a matching column is found for the requested key, null otherwise.
     */
    public function __get($key)
    {
        $row = $this->getRow();
        return isset($row->{$key}) ? $row->{$key} : null;
    }
}
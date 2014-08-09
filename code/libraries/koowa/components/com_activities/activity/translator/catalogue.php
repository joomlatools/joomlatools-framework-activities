<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Translator Catalogue
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityTranslatorCatalogue extends ComKoowaTranslatorCatalogueAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'prefix'  => 'KLS_ACTIVITY_',
        ));

        parent::_initialize($config);
    }

    /**
     * Generates a translation key that is safe for INI format
     *
     * Overloaded for avoiding key length limit.
     *
     * @param  string $string
     * @param  int    $limit    Max key length, should be larger then 0. If -1 no limit will be used.
     * @return string
     */
    public function generateKey($string, $limit = 40)
    {
        return parent::generateKey($string, -1);
    }
}
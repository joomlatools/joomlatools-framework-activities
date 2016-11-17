<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Activity Translator Interface.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityTranslatorInterface
{
    /**
     * Translates an activity format.
     *
     * @param string $string The activity format to translate.
     * @return string The translated activity format.
     */
    public function format(ComActivitiesActivityInterface $activity);

    /**
     * Translates an activity object.
     *
     * @param ComActivitiesActivityObjectInterface $object   The activity object.
     * @param string|null                          $language The language to translate the object to.
     * @return string The translated object.
     */
    public function object(ComActivitiesActivityObjectInterface $object, $language = null);

    /**
     * Fallback catalogue setter.
     *
     * @param KTranslatorCatalogueInterface $catalogue The fallback catalogue.
     * @return ComActivitiesActivityTranslatorInterface
     */
    public function setFallbackCatalogue(KTranslatorCatalogueInterface $catalogue);

    /**
     * Fallback catalogue getter.
     *
     * @return KTranslatorCatalogueInterface The fallback catalogue.
     */
    public function getFallbackCatalogue();

    /**
     * Activities token
     *
     * Tokens are activity objects being referenced in the activity format. They represent variables contained
     * in an activity message.
     *
     * @param ComActivitiesActivityInterface $activity
     * @return array A list containing ComActivitiesActivityObjectInterface objects.
     */
    public function getTokens(ComActivitiesActivityInterface $activity);

    /**
     * Activity language getter.
     *
     * Determines the language of a given activity.
     *
     * @param ComActivitiesActivityInterface $activity The activity.
     * @return string The language locale.
     */
    public function getLanguage(ComActivitiesActivityInterface $activity);
}
<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Activity Translator.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityTranslator extends KTranslatorAbstract implements ComActivitiesActivityTranslatorInterface, KObjectMultiton
{
    /**
     * Associative array containing previously calculated overrides.
     *
     * @var array
     */
    protected $_overrides;

    /**
     * Fallback catalogue.
     *
     * @var mixed
     */
    protected $_fallback_catalogue;

    /**
     * Activity tokens.
     *
     * @var array
     */
    protected $_tokens;

    /**
     * Holds de language of each activity format that has been translated.
     *
     * @var array
     */
    protected $_languages;

    public function __construct(KObjectConfig $config)
    {
        $this->_fallback_catalogue = $config->fallback_catalogue;

        parent::__construct($config);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('fallback_catalogue' => 'com:activities.translator.catalogue.default'));
        parent::_initialize($config);
    }

    /**
     * Translates an activity format.
     *
     * @param string $string The activity format to translate.
     * @return string The translated activity format.
     */
    public function format(ComActivitiesActivityInterface $activity)
    {
        $tokens = $this->getTokens($activity);
        $format = $activity->getActivityFormat();

        $parameters = array();

        foreach ($tokens as $key => $value)
        {
            if ($value instanceof ComActivitiesActivityObjectInterface && $value->getObjectName()) {
                $value = $value->getObjectName();
            }

            if (is_scalar($value)) {
                $parameters[$key] = $value;
            }
        }

        $formats = array();

        for ($i = 0; $i < 2; $i++)
        {
            $catalogue = $this->getCatalogue();

            if ($length = $catalogue->getConfig()->key_length) {
                $catalogue->getConfig()->key_length = false;
            }

            $formats[] = $this->translate($this->_getOverride($format, $parameters), array());

            if ($length) {
                $catalogue->getConfig()->key_length = $length;
            }

            $this->_switchLanguage();
        }

        list($main_format, $fallback_format) = $formats;

        // Determine the translation language and keep track of it
        if (!isset($this->_languages[$main_format]))
        {
            $language = ($main_format == $fallback_format) ? $this->getLocaleFallback() : $this->getLocale();

            $this->_languages[$main_format] = $language;
        }

        return $main_format;
    }

    /**
     * Translates an activity object.
     *
     * @param ComActivitiesActivityObjectInterface $object   The activity object.
     * @param string|null                          $language The language to translate the object to.
     * @return string The translated object.
     */
    public function getLanguage(ComActivitiesActivityInterface $activity)
    {
        $language = null;

        $format = $activity->getActivityFormat();

        if (isset($this->_languages[$format])) {
            $language = $this->_languages[$format];
        }

        return $language;
    }

    /**
     * Translates an activity object.
     *
     * @param ComActivitiesActivityObjectInterface $object   The activity object.
     * @param string|null                          $language The language to translate the object to.
     * @return string The translated object.
     */
    public function object(ComActivitiesActivityObjectInterface $object, $language = null)
    {
        $result = null;

        $result = $object->getDisplayName();

        if ($object->isTranslatable())
        {
            $language = is_null($language) ? $this->getLocale() : $language;

            if ($language != $this->getLocale())
            {
                // Use fallback catalogue instead
                $this->_switchLanguage();
                $result = $this->translate($result);
                $this->_switchLanguage();
            }
            else $result = $this->translate($result);
        }

        return $result;
    }

    /**
     * Fallback catalogue setter.
     *
     * @param KTranslatorCatalogueInterface $catalogue The fallback catalogue.
     * @return ComActivitiesActivityTranslatorInterface
     */
    public function setFallbackCatalogue(KTranslatorCatalogueInterface $catalogue)
    {
        $this->_fallback_catalogue = $catalogue;
        return $this;
    }

    /**
     * Fallback catalogue getter.
     *
     * @return KTranslatorCatalogueInterface The fallback catalogue.
     */
    public function getFallbackCatalogue()
    {
        if (!$this->_fallback_catalogue instanceof KTranslatorCatalogueInterface) {
            $this->setFallbackCatalogue($this->getObject($this->getConfig()->fallback_catalogue));
        }

        return $this->_fallback_catalogue;
    }

    /**
     * Get an activity format override.
     *
     * @param  string $format     The activity format.
     * @param  array  $parameters Associative array containing parameters.
     * @return string The activity format override. If an override was not found, the original activity format is
     *                returned instead.
     */
    protected function _getOverride($format, $parameters = array())
    {
        $override = $format;

        $locale = $this->getLocale();

        if (!isset($this->_overrides[$locale])) {
            $this->_overrides[$locale] = array();
        }

        if ($parameters)
        {
            $key = $this->_getOverrideKey($format, $parameters);

            if (!isset($this->_overrides[$locale][$key]))
            {
                foreach ($this->_getOverrides($format, $parameters) as $candidate)
                {
                    // Check if the override is translatable.
                    if ($this->isTranslatable($candidate))
                    {
                        $override = $candidate;
                        break;
                    }
                }

                $this->_overrides[$locale][$key] = $override;
            }
            else $override = $this->_overrides[$locale][$key];
        }

        return $override;
    }

    /**
     * Get an activity format override key.
     *
     * @param  string $format     The activity format.
     * @param  array  $parameters Associative array containing parameters.
     * @return string The activity format override key.
     */
    protected function _getOverrideKey($format, $parameters = array())
    {
        $result = $format;

        foreach ($parameters as $key => $value) {
            $result = str_replace(sprintf('{%s}', $key), $value, $result);
        }

        return $result;
    }

    /**
     * Returns a list of activity format overrides.
     *
     * @param  string $format     The activity format.
     * @param  array  $parameters Associative array containing parameters.
     * @return array A list of activity format overrides.
     */
    protected function _getOverrides($format, $parameters = array())
    {
        $overrides = array();

        if (!empty($parameters))
        {
            // Get the power set of the set of parameters and construct a list of string overrides from it.
            foreach ($this->_getPowerSet(array_keys($parameters)) as $subset)
            {
                $override = $format;

                foreach ($subset as $key) {
                    $override = str_replace(sprintf('{%s}', $key), $parameters[$key], $override);
                }

                $overrides[] = $override;
            }
        }

        return $overrides;
    }

    /**
     * Returns the power set of a set represented by the elements contained in an array.
     *
     * For convenience, the elements are ordered from size (subsets with more elements first).
     *
     * @param     array $set        The set to get the power set from.
     * @param     int   $min_length The minimum amount of elements that a subset from the power set may contain.
     * @return array The power set represented by an array of arrays containing elements from the provided set.
     */
    protected function _getPowerSet(array $set = array(), $min_length = 1)
    {
        $elements = count($set);
        $size     = pow(2, $elements);
        $members  = array();

        for ($i = 0; $i < $size; $i++)
        {
            $b      = sprintf("%0" . $elements . "b", $i);
            $member = array();
            for ($j = 0; $j < $elements; $j++) {
                if ($b{$j} == '1') $member[] = $set[$j];
            }

            if (count($member) >= $min_length)
            {
                if (!isset($members[count($member)])) {
                    $members[count($member)] = array();
                }

                // Group members by number of elements they contain.
                $members[count($member)][] = $member;
            }
        }

        // Sort members by number of elements (key value).
        ksort($members, SORT_NUMERIC);

        $power = array();

        // We want members with greater amount of elements first.
        foreach (array_reverse($members) as $subsets) {
            $power = array_merge($power, $subsets);
        }

        return $power;
    }

    /**
     * Loads translations from a url
     *
     * @param string $url      The translation url
     * @param bool   $override If TRUE override previously loaded translations. Default FALSE.
     * @return bool TRUE if translations are loaded, FALSE otherwise
     */
    public function load($url, $override = false)
    {
        if (!$this->isLoaded($url))
        {
            for ($i = 0; $i < 2; $i++)
            {
                $translations = array();

                foreach($this->find($url) as $file)
                {
                    try {
                        $loaded = $this->getObject('object.config.factory')->fromFile($file)->toArray();
                    } catch (Exception $e) {
                        return false;
                        break;
                    }

                    $translations = array_merge($translations, $loaded);
                }

                $this->getCatalogue()->add($translations, $override);

                // Switch catalogue and locale for loading translations on fallback locale.
                $this->_switchLanguage();
            }

            $this->_loaded[] = $url;
        }

        return true;
    }

    /**
     * Switches the translator language.
     *
     * The main catalogue and locale are switched by the fallback catalogue and locale.
     *
     * @return ComActivitiesActivityTranslatorInterface
     */
    protected function _switchLanguage()
    {
        // Switch Catalogues
        $catalogue = $this->getFallbackCatalogue();
        $this->setFallbackCatalogue($this->getCatalogue());
        $this->setCatalogue($catalogue);

        // Switch Locales
        $locale = $this->getLocaleFallback();
        $this->setLocaleFallback($this->getLocale());
        $this->_locale = $locale; // Do not use setter for avoiding clearing the catalogue and re-loading files

        return $this;
    }

    /**
     * Get the activity format tokens.
     *
     * Tokens are activity objects being referenced in the activity format.
     *
     * @return array An array containing ComActivitiesActivityObjectInterface objects
     */
    public function getTokens(ComActivitiesActivityInterface $activity)
    {
        $format = $activity->getActivityFormat();

        if (!$this->_tokens[$format])
        {
            $tokens = array();

            if (preg_match_all('/\{(.+?)\}/',$format, $labels))
            {
                $objects = $activity->getActivityObjects();

                foreach ($labels[1] as $label)
                {
                    $object = null;
                    $parts  = explode('.', $label);

                    if (count($parts) > 1)
                    {
                        $name = array_shift($parts);

                        if (isset($objects[$name]))
                        {
                            $object = $objects[$name];

                            foreach ($parts as $property)
                            {
                                $object = $object->{$property};
                                if (is_null($object)) break;
                            }
                        }
                    }
                    else
                    {
                        if (isset($objects[$label])) {
                            $object = $objects[$label];
                        }
                    }

                    if ($object instanceof ComActivitiesActivityObjectInterface) {
                        $tokens[$label] = $object;
                    }
                }
            }
        }
        else $tokens = $this->_tokens[$format];

        return $tokens;
    }
}
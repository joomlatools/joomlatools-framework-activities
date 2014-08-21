<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-activities for the canonical source repository
 */

/**
 * Activity Translator
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesActivityTranslator extends ComKoowaTranslator implements KObjectSingleton
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'catalogue' => 'com:activities.activity.translator.catalogue'
        ));

        parent::_initialize($config);
    }

    /**
     * Translates an activity and handles parameter replacements
     *
     * Activity parameters are wrapped in curly braces. So {foo} would be replaced with bar given that
     * $parameters['foo'] = 'bar'
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $tokens = array())
    {
        $override = $this->_getOverride($string, $tokens);

        return parent::translate($string, array());
    }

    /**
     * Get an activity override based on an activity format
     *
     * @param  string $format The activity format.
     * @param  array $tokens An array of ComActivitiesActivityObjectInterface objects representing format tokens.
     *
     * @return array A list of override strings.
     */
    protected function _getOverride($format, $tokens = array())
    {
        $override = $format;

        if ($tokens)
        {
            foreach ($this->_getOverrides($format, $tokens) as $candidate)
            {
                // Check if the override is translatable.
                if ($this->isTranslatable($candidate))
                {
                    $override = $candidate;
                    break;
                }
            }
        }

        return $override;
    }

    /**
     * Returns a list of activity format overrides.
     *
     * @param  string $format The activity format.
     * @param  array $tokens An array of ComActivitiesActivityObjectInterface objects representing format tokens.
     *
     * @return array A list of override strings.
     */
    protected function _getOverrides($format, $tokens = array())
    {
        $overrides = array();
        $set       = array();

        // Construct a set of non-empty tokens.
        foreach ((array) $tokens as $label => $object)
        {
            if ($object instanceof ComActivitiesActivityObjectInterface && $object->getObjectName()) {
                $set[] = $label;
            }
        }

        if (count($set))
        {
            // Get the power set of the set of parameters and construct a list of string overrides from it.
            foreach ($this->_getPowerSet($set) as $subset)
            {
                $override = $format;

                foreach ($subset as $label)
                {
                    $object   = $tokens[$label];
                    $override = str_replace('{' . $label . '}', $object->getObjectName(), $override);
                }

                $overrides[] = $override;
            }
        }

        return $overrides;
    }

    /**
     * Returns the power set of a set represented by the elements contained in an array.
     *
     * The elements are ordered from size (subsets with more elements first) for convenience.
     *
     * @param     array $set        The set to get the power set from.
     * @param     int   $min_length The minimum amount of elements that a subset from the power set may contain.
     *
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
}

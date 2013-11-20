<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://github.com/joomlatools/koowa-activities for the canonical source repository
 */

/**
 * Activity Translator
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
class ComActivitiesTranslatorActivity extends ComKoowaTranslator implements ComActivitiesTranslatorInterface
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'alias_catalogue' => 'koowa:translator.catalogue',
            'prefix'          => 'KLS_ACTIVITY_',
            'catalogue'       => 'com:activities.translator.catalogue.activity'
        ));

        parent::_initialize($config);
    }

    /**
     * @see ComActivitiesTranslatorInterface::translate()
     *
     * Overridden to look for translation overrides and provide support for context translations.
     */
    public function translate($string, array $parameters = array())
    {
        if ($parameters)
        {
            foreach ($this->_getOverrides($string, $parameters) as $override)
            {
                // Check if a key for the $override exists.
                if ($this->isTranslatable($override))
                {
                    $string = $override;
                    break;
                }
            }
        }

        $replacements = array();

        foreach ($parameters as $parameter) {
            $replacements[$parameter->getLabel()] = $parameter->render();
        }

        $translation = parent::translate($string, $replacements);

        // Process context translations.
        if (preg_match_all('/\{(.+?):(.+?)\}/', $translation, $matches) !== false)
        {
            for ($i = 0; $i < count($matches[0]); $i++)
            {
                $label = $matches[1][$i];

                foreach ($parameters as $parameter)
                {
                    if ($parameter->getLabel() == $label)
                    {
                        $clone = clone $parameter;
                        // Change the text for the one provided by the translation.
                        $clone->setText($matches[2][$i])->setTranslatable(false);
                        //  Make the parameter non-translatable (as it's already translated).
                        $translation = str_replace($matches[0][$i], $clone->render(), $translation);
                        break;
                    }
                }
            }
        }

        return $translation;
    }

    /**
     * Returns a list of override strings for the provided string/parameters couple.
     *
     * @param     string                                      $string             The activity string.
     * @param     ComActivitiesTranslatorParameterInterface[] $parameters         An optional array containing translator
     *                                                                            parameter objects.
     *
     * @return array A list of override strings.
     */
    protected function _getOverrides($string, array $parameters = array())
    {
        $overrides = array();
        $set       = array();

        // Construct a set containing non-empty (with replacement texts) parameters.
        foreach ($parameters as $parameter)
        {
            if ($parameter->getText()) {
                $set[] = $parameter;
            }
        }

        if (count($set))
        {
            // Get the power set of the set of parameters and construct a list of string overrides from it.
            foreach ($this->_getPowerSet($set) as $subset)
            {
                $override = $string;
                foreach ($subset as $parameter)
                {
                    $override = str_replace('{' . $parameter->getLabel() . '}', $parameter->getText(),
                        $override);
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
     * @param     array $set          The set to get the power set from.
     * @param     int   $min_length   The minimum amount of elements that a subset from the power set may contain.
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
            for ($j = 0; $j < $elements; $j++)
            {
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
     * @see ComActivitiesTranslatorInterface::parse()
     */
    public function parse($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException('$string must be of type string');
        }

        $components = array('parameters' => array(), 'words' => array());

        if (preg_match_all('/\{(.+?)\}/', $string, $matches) !== false) {
            $components['parameters'] = $matches[1];
        }

        if (preg_match_all('/(?:^|\s+)(\w+?)(?:$|\s+)/', $string, $matches) !== false) {
            $components['words'] = $matches[1];
        }

        return $components;
    }
}

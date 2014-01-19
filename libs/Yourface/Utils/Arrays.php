<?php

namespace Yourface\Utils;

/**
 * Arrays helpers.
 *
 * @author Lukas Bruha
 */
class Arrays {
    
    /**
     * Searches an array for an items and removes it.
     * 
     * @param array $array
     * @param string|array $needles
     * 
     */
    public static function searchAndRemove(&$array, $needles, $softRemove = false) {
        // converts simple structures to array
        if (!is_array($needles)) {
            $needles = array($needles);
        }
        
        if (count($array)) {
            foreach ($array as $name => &$field) {
                if (in_array($name, $needles, true)) {
                    // soft remove only mark as removed
                    if ($softRemove) {
                        $array[$name] = '** REMOVED **';
                    } else {
                        unset($array[$name]);
                        continue;
                    }
                }

                // continue recursively if field is an array
                if (is_array($field)) {
                    self::searchAndRemove($field, $needles);
                }
            }
        }
    }
}
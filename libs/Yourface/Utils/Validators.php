<?php

namespace Yourface\Utils;

/**
 * Validators enhancement.
 *
 * @author Lukas Bruha
 */
class Validators {

    /**
     * Checks if value includes spaces.
     * 
     * @param string $value
     * @return boolean
     */
    public static function hasLettersAndNumbersOnly($value) {
        if (preg_match("/^[A-Za-z0-9]+$/", $value)) {
            return true;
        }

        return false;
    }

    public static function isJson($input) {
        json_decode($input);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}

<?php

namespace Yourface\Utils;

/**
 * View helpers and converters.
 * 
 * @author Lukas Bruha
 */
class Helpers {

    /**
     * Converts datetime to words.
     * 
     * @param string|DateTime $datetime
     * @param int $limit Seconds above that conversion is ignored.
     * @return type
     */
    public static function ago($datetime, $limit = false) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;

        if ($difference < 5) {
            return "now";
        } else
        if ($difference <= $limit) {
            $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
            $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
            for ($j = 0; $difference >= $lengths[$j]; $j++)
                $difference /= $lengths[$j];

            $difference = round($difference);
            if ($difference != 1)
                $periods[$j].= "s";

            $text = "$difference $periods[$j] ago";
            return $text;
        } elseif ($datetime && (date('H', $timestamp) < date('H', time())) && (date('Ymd') == date('Ymd', $timestamp))) {
            return "today at " . date('H:i', $timestamp);
        }
        return $datetime;
    }

    public static function json_unescaped_unicode($array) {
        //return json_decode($array, JSON_UNESCAPED_UNICODE);
        
       return preg_replace_callback('/\\\u(\w\w\w\w)/', function($matches) {
                return '&#'.hexdec($matches[1]).';';        
        }, stripslashes(json_encode($array, true)));
    }

}

<?php
namespace Lycee;

class Check extends Config {

    public static function isIntBetween($subject, $min, $max) {
        if (!is_int($subject)) {
            return false;
        } 
        if ($subject<$min or $subject>$max) {
            return 0;
        }
        return true;
    }
    
    public static function isValidExValue($x) {
        return ($x < 0 or $x > Config::MAX_EX_VALUE) ? false : true;
    }
    
    public static function isValidElement($x) {
        return ($x < 0 or $x > 4) ? false : true;
    }
    
    public static function isValidElementCost($x) {
        return ($x < 0 or $x > Config::MAX_ELEMENT_COST) ? false : true;
    }
    
    public static function isValidStat($x) {
        return ($x < 0 or $x > Config::MAX_STAT_VALUE) ? false : true;
    }
    
    public static function isValidSpot($x) {
        return ($x < 0 or $x > 63) ? false : true;
    }
    
    public static function isValidBasicAbility($x) {
        return ($x < Config::MIN_BASIC_ABILITY or $x > Config::MAX_BASIC_ABILITY) ? false : true;
    }
}
?>

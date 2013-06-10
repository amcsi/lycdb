<?php
class CostElement extends Lycee {
    
    public static function makeArray($snow, $moon, $lightning, $flower, $sun, $star) {
        // throws OutOfRangeException or InvalidArgumentException.
        if (!self::checkArrayArgs($args = func_get_args())) {
            return false;
        }
        return $args;
    }
    
    public static function checkArray($costElementArray) {
        if (checkArrayArgs($costElementArray) && checkArraySum($costElementArray)) {
            return true;
        }
        return false;
    }
    
    public static function checkArrayArgs($costElementArray) {
        if (count($costElementArray) != 6) {
            return false;
        }
        // checking argument validity
        foreach($costElementArray as $key => &$val) {
            // is it an integer between 0 and 12?
            if (!Check::isValidElementCost($val)) {
                return false;
            }
            else {
                $val = (int) $val;
            }
        }
        // do the sum of the arguments not exceed 12?
        
        return true;
    }
    public static function checkArraySum($costElementArray) {
        if (!Check::isValidElementCost(array_sum($costElementArray))) {
            return false;
        }
        return true;
    }
}
?>
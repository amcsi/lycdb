<?php
namespace Lycee;

class Bw {
    
    // returns a number of bits turned on starting from the right side, continuously for the value of $size; 
    public static function sizeToBits($size) {
        return ((1 << $size) -1);
    }

    // returns an integer with selected bits turned on    
    public static function selectBits($position, $size = 1) {
        return (self::sizeToBits($size) << $position);
    }
    
    // clears selected bits
    public static function clearBits($subject, $position, $size = 1) {
        return ~self::selectBits($position, $size) & $subject;
    }
    
    // changes selected bits to a new value
    public static function changeBits($subject, $position, $size, $newValue) {
        return self::clearBits($subject, $position, $size) | 
            ($newValue << $position);
    }
    
    // returns the wanted bits, shifted all the way to the right
    public static function getBits($subject, $position, $size = 1) {
        return (self::selectBits($position, $size) & $subject) >> $position;
    }
}
?>

<?php
abstract class Lycee {
    const STAR = 5;
    const SUN = 4;
    const LIGHTNING = 3;
    const FLOWER = 2;
    const MOON = 1;
    const SNOW = 0;
    
    const NO_STAR = 4;
    
    const MAX_COST = 12;
    
    public static function elementIntToLcString($element) {
        switch($element) {
            case SNOW:
                return 'snow';
            case MOON:
                return 'moon';
            case LIGHTNING:
                return 'lightning';
            case FLOWER:
                return 'flower';
            case SUN:
                return 'sun';
            case STAR:
                return 'star';
            default:
                throw new InvalidArgumentException;
        }
    }
    
    protected function checkElement($element) {
        $check = Check::isIntBetween($element,0,5);
        if (!$check) {
            throw new MyException
                ("Element error!",485086087,$amount);
        }
    }
    
    protected function checkCostValue($amount) {
        $check = Check::isIntBetween($amount,0,MAX_COST);
        if (!$check) {
            if ($check===false) {
                throw new MyException 
                    ("Cost amount type isn't an integer!",557291834,$amount);
            } else {
                throw new MyException 
                    ("Cost amount must be between 0 and ".maxCost."!",639414439,$amount);
            }
        }
    }
    
}
?>

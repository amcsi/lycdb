<?php
namespace Lycee;

class Char extends Card {
    
    protected $isMale = false;
    protected $isFemale = false;
    protected $ap;
    protected $dp;
    protected $sp;
    protected $spotFlags;
    protected $basicAbilities = array(); // Key represents the type. Holds an onject or TRUE as a value
    public $abilityNames = array ();
    public $abilityCostObj;
    
    // negative basic abilities do not have costs
    const DASH          = -2;
    const AGGRESSIVE    = -1;
    const STEP          = 0;
    const SIDE_STEP     = 1;
    const ORDER_STEP    = 2;
    const JUMP          = 3;
    const ESCAPE        = 4;
    const SIDE_ATTACK   = 5;
    const TAX_TRASH     = 6;
    const TAX_WAKEUP    = 7;
    const SUPPORTER     = 8;
    const TOUCH         = 9;
    const ATTACKER      = 10;
    const DEFENDER      = 11;
    const BONUS         = 12;
    const PENALTY       = 13;
    const DECK_BONUS    = 14;
    
    const STAT_AP = 0;
    const STAT_DP = 1;
    const STAT_SP = 2;
    
    const AL_FLAG = 1;
    const AC_FLAG = 2;
    const AR_FLAG = 4;
    const DL_FLAG = 8;
    const DC_FLAG = 16;
    const DR_FLAG = 32;
    
    function setIsMale($bool) {
        $this->isMale = (bool) $bool;
    }
    
    function setIsFemale($bool) {
        $this->isFemale = (bool) $bool;
    }
    
    function setStat($statInt, $value) {
        if (!isset($statInt, $value)) {
            return false;
        }
        if (!Check::isValidStat) {
            return false;
        }
        $value = (int) $value;
        switch ($statInt) {
            case STAT_AP:
                $this->ap = $value;
                break;
            case STAT_DP:
                $this->dp = $value;
                break;
            case STAT_SP:
                $this->sp = $value;
                break;
            default:
                return false;
                break;
        }
        return true;
    }
    
    public function setSpotFlags($spotFlags) {
        if (!isset($spotFlags)) {
            return false;
        }
        if (!Check::isValidSpot($spotFlags)) {
            return false;
        }
        $this->spotFlags = (int) $spotFlags;
        return true;
    }
    
    public function setBasicAbility($basicAbilityInt, $bool, $costObj = false) {
        if (!isset($basicAbilityInt, $bool, $costObj)) {
            return false;
        }
        if (!Check::isValidBasicAbility($basicAbilityInt)) {
            return false;
        }
        $basicAbilityInt = (int) $basicAbilityInt;
        if (!$bool) {
            unset($this->basicAbilities[$basicAbilityInt]);
            return true;
        }
        if ($basicAbilityInt < 0) {
            $this->basicAbilities[$basicAbilityInt] = true;
            return true;
        }
        // if the basic ability key is at least 0, its value must be a Cost object.
        if ($costObj instanceof Cost) {
            $this->basicAbilities[$basicAbilityInt] = $costObj;
            return true;
        }
        return false;
    }
    
    public function searchAreSpots($searchSpotFlags, $isAnd = false) {
        if (!isset($searchSpotFlags, $isAnd)) {
            return false;
        }
        if (!Check::isValidSpot($spotFlags)) {
            return false;
        }
        if (!$spotFlags) {
            return 1;
        }
        if ($asAnd) {
            return
                ($this->spotFlags | $searchSpotFlags == $this->spotFlags) ?
                1 :
                0;
        } else {
            return ($this->spotFlags & $searchSpotFlags) ? 1 : 0;
        }
    }
    
    public function isObjectComplete() {
        return (
            isset(
                $this->isMale,
                $this->isFemale,
                $this->ap,
                $this->dp,
                $this->sp,
                $this->spotFlags,
                $this->abilityNames,
                $this->abilityCostObj
            ) 
            and parent::isObjectComplete()
        );
    }
}
?>

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
    public $abilityTexts = array ();
    public $abilityCostObj;
    public $conversion = '';
    
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
    const DASH          = 15;
    const AGGRESSIVE    = 16;
    const BOOST         = 17;
    
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
        switch ($statInt) {
            case self::STAT_AP:
                $this->ap = $value;
                break;
            case self::STAT_DP:
                $this->dp = $value;
                break;
            case self::STAT_SP:
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

    public function setGenderByText($gender) {
        $isMale = false !== strpos($gender, 'm') || false !== strpos($gender, 'M') ||
            false !== strpos($gender, '男') || false !== strpos($gender, '♂')
        ;
        $isFemale = false !== strpos($gender, 'f') || false !== strpos($gender, 'F') ||
            false !== strpos($gender, '女') || false !== strpos($gender, '♀')
        ;
        $this->isMale = $isMale;
        $this->isFemale = $isFemale;
    }

    public function setSpecialAbilityName($name) {
        $this->abilityNames[self::LANG_JP] = $name;
    }

    public function setSpecialAbilityCost(Cost $cost) {
        $this->abilityCostObj = $cost;
    }

    public function setSpecialAbilityText($text) {
        $this->abilityTexts[self::LANG_JP] = $text;
    }

    public function getJapaneseBasicAbilityMap() {
        static $ret = array (
            'ダッシュ'                  => self::DASH,
            'アグレッシブ'              => self::AGGRESSIVE,
            'ステップ'                  => self::STEP,
            'サイドステップ'            => self::SIDE_STEP,
            'サイド・ステップ'          => self::SIDE_STEP,
            'サイド･ステップ'           => self::SIDE_STEP,
            'オーダーステップ'          => self::ORDER_STEP,
            'オーダー・ステップ'        => self::ORDER_STEP,
            'オーダー･ステップ'         => self::ORDER_STEP,
            'ジャンプ'                  => self::JUMP,
            'エスケープ'                => self::ESCAPE,
            'サイドアタック'            => self::SIDE_ATTACK,
            'タックストラッシュ'        => self::TAX_TRASH,
            'タックス・トラッシュ'      => self::TAX_TRASH,
            'タックス･トラッシュ'       => self::TAX_TRASH,
            'タックスウェイクアップ'    => self::TAX_WAKEUP,
            'タックス・ウェイクアップ'  => self::TAX_WAKEUP,
            'タックス･ウェイクアップ'   => self::TAX_WAKEUP,
            'サポーター'                => self::SUPPORTER,
            'タッチ'                    => self::TOUCH,
            'アタッカー'                => self::ATTACKER,
            'ディフェンダー'            => self::DEFENDER,
            'ボーナス'                  => self::BONUS,
            'ペナルティ'                => self::PENALTY,
            'デッキボーナス'            => self::DECK_BONUS,
            'デッキ・ボーナス'          => self::DECK_BONUS,
            'デッキ･ボーナス'           => self::DECK_BONUS,
            'ブースト'                  => self::BOOST,
        );
        return $ret;
    }
    
    public function getJapaneseBasicAbilityFlippedMap() {
        static $ret;
        if (!$ret) {
            $ret = array_flip($this->getJapaneseBasicAbilityMap());
        }
        return $ret;
    }

    public function getEnglishBasicAbilityMap() {
        static $ret = array (
            'Dash'                  => self::DASH,
            'Aggressive'              => self::AGGRESSIVE,
            'Step'                  => self::STEP,
            'Side Step'            => self::SIDE_STEP,
            'Order Step'          => self::ORDER_STEP,
            'Jump'                  => self::JUMP,
            'Escape'                => self::ESCAPE,
            'Side Attack'            => self::SIDE_ATTACK,
            'Tax Trash'      => self::TAX_TRASH,
            'Tax Wakeup'  => self::TAX_WAKEUP,
            'Supporter'                => self::SUPPORTER,
            'Touch'                    => self::TOUCH,
            'Attacker'                => self::ATTACKER,
            'Defender'            => self::DEFENDER,
            'Bonus'                  => self::BONUS,
            'Penalty'                => self::PENALTY,
            'Deck Bonus'          => self::DECK_BONUS,
        );
        return $ret;
    }
    
    public function getEnglishBasicAbilityFlippedMap() {
        static $ret;
        if (!$ret) {
            $ret = array_flip($this->getEnglishBasicAbilityMap());
        }
        return $ret;
    }

    public function basicAbilityHasCost($basicAbilityInt) {
        static $noCosts = array (
            self::DASH, self::AGGRESSIVE, self::ATTACKER, self::DEFENDER
        );
        $hasCost = !in_array($basicAbilityInt, $noCosts);
        return $hasCost;
    }

    public function toDbData() {
        $data = parent::toDbData();
        $data['type'] = self::CHAR;
        $abilityCostObj = $this->abilityCostObj;
        if ($abilityCostObj) {
            $data['ability_cost_jp'] = $abilityCostObj->toLycdbMarkup();
            $data['ability_cost_en'] = $abilityCostObj->toLycdbMarkup();
        }
        $data['ability_name_jp'] = $this->abilityNames[self::LANG_JP];
        $data['ability_name_en'] = isset($this->abilityNames[self::LANG_EN]) ? $this->abilityNames[self::LANG_EN] : null;
        $data['conversion_jp'] = $this->conversion;
        $data['basic_ability_flags'] = $this->getBasicAbilityFlags();
        $basicAbilitiesJp = array ();
        $basicAbilitiesEn = array ();
        $basicAbilitiesJpFlipped = $this->getJapaneseBasicAbilityFlippedMap();
        $basicAbilitiesEnFlipped = $this->getEnglishBasicAbilityFlippedMap();
        foreach ($this->basicAbilities as $key => $val) {
            $babJp = $basicAbilitiesJpFlipped[$key];
            $babEn = $basicAbilitiesEnFlipped[$key];
            if ($val instanceof Cost) {
                $textPart = $val->toLycdbMarkup();
                $babEn .= " $textPart";
                $babJp .= " $textPart";
            }
            $basicAbilitiesJp[] = $babJp;
            $basicAbilitiesEn[] = $babEn;
        }
        $data['basic_abilities_jp'] = join("\n", $basicAbilitiesJp);
        $data['basic_abilities_en'] = join("\n", $basicAbilitiesEn);
        $data['is_male']            = $this->isMale ? 1 : 0;
        $data['is_female']            = $this->isFemale ? 1 : 0;
        return $data;
    }

    public function getBasicAbilityFlags() {
        $keys = array_keys($this->basicAbilities);
        $flags = 0;
        foreach ($keys as $key) {
            $flags |= 1 << $key;
        }
        return $flags;
    }
}
?>

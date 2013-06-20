<?php
namespace Lycee;
use \BadMethodCallException;

abstract class Card extends Lycee {

    public $cid; // card ID
    public $rarity;
    public $nameJap;
    public $nameEng;

    protected $set;
    /**
     * @var array
     * @access protected
     */
    protected $cost;
    public $ex;
    protected $elementFlags;
    protected $texts; // array
    protected $comments;

    public $setExtId;

    protected $_errors = array ();
    
    const CHAR = 0;
    const AREA = 1;
    const ITEM = 2;
    const EVENT = 3;
    
    const MAX_EX_VALUE = 3;
    
    const LANG_JP = 0;
    const LANG_EN = 1;
    
    public function __call($name, $args) {
        if (substr($name, 0, 3) == 'get') {
            $prop = lcfirst(substr(__call, 3));
            if (property_exists(__CLASS__, $prop)) {
                switch ($prop) {
                    case 'elementFlags':
                        throw new BadMethodCallException;
                        break;
                    default:
                        if (is_bool($this->$prop)) {
                            return (int) $this->$prop;
                        }
                        return $this->$prop;
                } 
            }
            else {
                throw new BadMethodCallException;
            }
        }
        else {
            throw new BadMethodCallException;
        }
    }

    public function setElementByJapaneseArray($array) {
        $map = $this->getJapaneseElementMap();
        foreach ($array as $japaneseElement => $bool) {
            $enumVal = $map[$japaneseElement];
            if (is_int($enumVal)) {
                $this->insertElement($enumVal, $bool);
            }
            else {
                trigger_error("Bad japanese element: `$japaneseElement`");
            }
        }
    }

    public function setCostByJapaneseArray($array) {
        $map = $this->getJapaneseElementMap();
        $costArray = array ();
        foreach ($array as $japaneseElement => $amount) {
            $enumVal = $map[$japaneseElement];
            if (is_int($enumVal)) {
                $costArray[$enumVal] = $amount;
            }
            else {
                trigger_error("Bad japanese element: `$japaneseElement`");
            }
        }
        $this->cost = $costArray;
    }
    
    public function setText($lang, $text) {
        $this->texts[(int) $lang] = $text;
        return $this;
    }
    
    public function setComment($lang, $text) {
        if (!isset($lang, $text)) {
            return false;
        }
        $this->comments[(int) $lang] = $text;
        return $this;
    }
    
    public function isType($type) {
        switch($type) {
            case char: return ($this instanceof Char)? 1 : 0 ; break;
            case area: return ($this instanceof Area)? 1 : 0; break;
            case item: return ($this instanceof Item)? 1 : 0; break;
            case event: return ($this instanceof Event)? 1 : 0; break;
            default: return false; break;
        }
    }
    
    public function getElement($element) {
        if (!Check::isValidElement($element)) {
            return false;
        }
        return (Bw::getBits($this->elementFlags, $element, 1));
    }
    
    public function findSet() {
        // coming soon
    }
    
    public function getName($isInRomaji=false) {
        return ($isInRomaji and $this->nameEng) ? $this->nameEng : 
        $this->nameJap;
    }
    
    public static function newCardByTypeText($typeText) {
        switch (strtolower($typeText)) {
        case 'character':
            return new Char;
        case 'area':
            return new Area;
        case 'event':
            return new Event;
        case 'item':
            return new Item;
        default:
            trigger_error("No such card type: $typeText");
        }
    }
    
    
    public function getTextByPriority($firstLang = LANG_ENG, $secondLang = false) {
        if ($firstLang) {
            if (array_key_exists($this->text[$firstLang]) ) {
                return $this->text[$firstLang];
            }
            if ($secondLang) {
                if (array_key_exists($this->text[$secondLang]) ) {
                    return $this->text[$secondLang];
                }
            }
        }
        return $this->text[LANG_JAP];
    }
    
    public function getCommentByPriority($firstLang = LANG_ENG, $secondLang = false) {
        if ($this->comments === false) {
            return false;
        }
        if ($firstLang) {
            if (array_key_exists($this->comments[$firstLang]) ) {
                return $this->comments[$firstLang];
            }
            if ($secondLang) {
                if (array_key_exists($this->comments[$secondLang]) ) {
                    return $this->comments[$secondLang];
                }
            }
        }
        return $this->comments[LANG_JAP];
    }
    
    public function getCostElement($element) {
        if (!isset($element)) {
            return false;
        }
        if (!Check::isIntBetween($element, 0, STAR)) {
            return false;
        }
        return isset($this->cost[$element]) ? $this->cost[$element] : 0;
    }
    
    public function insertElement($element, $boolean) {
        if (!isset($element, $boolean)) {
            return false;
        }
        $this->elementFlags = Bw::changeBits($this->elementFlags, 0, 1, (int) $boolean);
        return true;
    }
    
    public function isObjectComplete() {
        return isset(
            $this->cid,
            $this->nameJap,
            $this->cost,
            $this->ex,
            $this->elementFlags,
            $this->nameEng,
            $this->texts,
            $this->comments
        );
    }

    public function setMainAbilityText($abilityText) {
        $this->abilityTexts[self::LANG_JP] = $abilityText;
    }

    public function setJpName($name) {
        $this->nameJap = $name;
    }

    public function setCidText($cidText) {
        $pattern = "@(\w+)-(\d+)([A-Z])?@";
        $success = preg_match($pattern, $cidText, $matches);
        if ($success) {
            $this->cid = intval($matches[2], 10);
        }
        else {
            trigger_error("Card id text did not match pattern. Text: `$cidText`");
        }
    }

    public function addError($error) {
        $this->_errors[] = $error;
    }

    public function getErrors() {
        return $this->_errors;
    }
}

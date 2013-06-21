<?php
namespace Lycee;
use \BadMethodCallException;

abstract class Card extends Lycee {

    public $cid; // card ID
    public $rarity;
    public $nameJap;
    public $nameEng;

    public $alternate = '';

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
    
    public static function newCardByTypeText($typeText, $cidText = null) {
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
            $msg = "No such card type: $typeText";
            if ($cidText) {
                $split = explode('-', $cidText);
                switch ($split[0]) {
                case 'CH':
                    return new Char;
                case 'AR':
                    return new Area;
                case 'EV':
                    return new Event;
                case 'IT':
                    return new Item;
                }
            }
            trigger_error($msg);
            $ret = new Char;
            $ret->addError($msg);
            return $ret;
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
        if (!Check::isIntBetween($element, 0, self::STAR)) {
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

    public function getCidText() {
        return $this->cidText;
    }

    public function setCidText($cidText) {
        $this->fullCidText = $cidText;
        $cidPattern = "@\w+-\d+@";
        $success = preg_match($cidPattern, $cidText, $matches);
        $this->cidText = $matches[0];
        $pattern = "@(\w+)-(\d+)([A-Z])?@";
        $success = preg_match($pattern, $cidText, $matches);
        if ($success) {
            $this->cid = intval($matches[2], 10);
        }
        else {
            trigger_error("Card id text did not match pattern. Text: `$cidText`");
        }
        if (!empty($matches[3])) {
            $this->alternate = $matches[3];
        }
    }

    public function addError($error) {
        $this->_errors[] = $error;
    }

    public function getErrors() {
        return $this->_errors;
    }

    public function toDbData() {
        $data = array ();
        $data['cid']                     = $this->getCidText();
        $data['name_jp']                 = $this->nameJap;
        $data['name_en']                 = $this->nameEng;
        $data['rarity']                  = $this->rarity;
        $data['ex']                      = $this->ex;
        $data['is_snow']                 = $this->getElement(self::SNOW);
        $data['is_moon']                 = $this->getElement(self::MOON);
        $data['is_lightning']                 = $this->getElement(self::LIGHTNING);
        $data['is_flower']                 = $this->getElement(self::FLOWER);
        $data['is_sun']                 = $this->getElement(self::SUN);
        $data['cost_snow']                 = $this->getCostElement(self::SNOW);
        $data['cost_moon']                 = $this->getCostElement(self::MOON);
        $data['cost_lightning']                 = $this->getCostElement(self::LIGHTNING);
        $data['cost_flower']                 = $this->getCostElement(self::FLOWER);
        $data['cost_sun']                 = $this->getCostElement(self::SUN);
        $data['cost_star']                 = $this->getCostElement(self::STAR);
        $data['ability_desc_jp']         = $this->texts[self::LANG_JP];
        $data['ability_desc_en']         = $this->texts[self::LANG_EN];
        $data['comments_jp']            = isset($this->comments[self::LANG_JP]) ? $this->comments[self::LANG_JP] : '';
        $data['comments_en']            = isset($this->comments[self::LANG_EN]) ? $this->comments[self::LANG_EN] : '';
        $data['import_errors']          = join("\n", $this->getErrors());

        $data['type'] = null;
        $data['ability_cost_jp'] = '';
        $data['ability_cost_en'] = '';
        $data['ability_name_jp'] = '';
        $data['ability_name_en'] = '';
        $data['conversion_jp'] = '';
        $data['basic_ability_flags'] = '';
        $data['basic_abilities_jp'] = '';
        $data['basic_abilities_en'] = '';
        $data['is_male']            = 0;
        $data['is_female']            = 0;
        $data['locked']            = 0;
        return $data;
    }
}

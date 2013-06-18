<?php
namespace Lycee;
use \BadMethodCallException;

abstract class Card extends Lycee {

    public $cid; // card ID
    public $rarity;
    public $nameJap;
    public $nameEng;

    protected $set;
    protected $costElement;
    protected $exValue;
    protected $exElementFlags;
    protected $texts; // array
    protected $comments;
    
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
                    case 'exElementFlags':
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
    
    public function getExIsElement($element) {
        if (!Check::isValidExElement($element)) {
            return false;
        }
        return (Bw::getBits($this->exElementFlags, $element, 1));
    }
    
    public function findSet() {
        // coming soon
    }
    
    public function getName($isInRomaji=false) {
        return ($isInRomaji and $this->nameEng) ? $this->nameEng : 
        $this->nameJap;
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
        return $this->costElement[$element];
    }
    
    public function insertExIsElement($element, $boolean) {
        if (!isset($element, $boolean)) {
            return false;
        }
        $this->isExElementFlags = Bw::changeBits($this->exIsElementFlags, 0, 1, (int) $boolean);
        return true;
    }
    
    public function isObjectComplete() {
        return isset(
            $this->cid,
            $this->nameJap,
            $this->costElement,
            $this->exValue,
            $this->exElementFlags,
            $this->nameEng,
            $this->texts,
            $this->comments
        );
    }
}





?>

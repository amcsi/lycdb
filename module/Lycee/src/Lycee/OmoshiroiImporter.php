<?php
namespace Lycee;

class OmoshiroiImporter {

    public function importByHtmlFile($htmlFile) {
        $doc = @\DomDocument::loadHTMLFile($htmlFile);
        $tables = $doc->getElementsByTagName('table');
        // table should only have an attribute of width="600"
        return $this->importByHtmlTable($tables->item(7));
    }

    public function importByHtmlTable(\DomElement $table) {
        $badBgColors = array (
            '#0099ff',
            '#83F290',
            '#83E0F7', // set
            '#ff9900', // set
        );
        $secondRowBgColor = '#eef3ff';
        $lastBgColor = '#eef3ff';
        $lastCardType = null;
        $pair = array ();
        $cardList = array ();
        $firstTdBgColor = null;
        foreach ($table->childNodes as $tr) {
            if (!$tr instanceof \DomElement) {
                continue;
            }
            foreach ($tr->childNodes as $td) {
                if (!$td instanceof \DomElement) {
                    continue;
                }
                else {
                    $bgColor = $td->getAttribute('bgcolor');
                    $cardType = $this->getCardTypeByBgColor($bgColor);
                    if ($cardType) {
                        $lastCardType = $cardType;
                    }
                    if (is_int($cardType)) {
                        $firstRow = $tr;
                    }
                    else if ($secondRowBgColor == $bgColor) {
                        $cardList[] = $this->cardBy2TrsInAbilityView($firstRow, $tr, $lastCardType);
                    }
                    break;
                }
            }
        }

    }

    public function getCardTypeByBgColor($bgColor) {
        static $map = array (
            '#ccccff' => Card::CHAR,
            '#ffcccc' => Card::EVENT,
            '#eeee99' => Card::ITEM,
            '#99ee99' => Card::AREA,
        );
        return isset($map[$bgColor]) ? $map[$bgColor] : null;
    }

    public function cardBy2TrsInAbilityView(\DomElement $tr1, \DomElement $tr2, $cardType) {
        $isChar = false;
        if (Card::CHAR == $cardType) {
            $isChar = true;
            $card = new Char();
        }
        else if (Card::EVENT == $cardType) {
            $card = new Event();
        }
        else if (Card::ITEM == $cardType) {
            $card = new Item();
        }
        else if (Card::AREA == $cardType) {
            $card = new Area();
        }
        else {
            trigger_error("Invalid bgColor: $firstBgColor");;
        }

        $tds1 = $tr1->getElementsByTagName('td');

        /**
         * Set
         **/
        $tdId = $tds1->item(0);
        $idText = $this->getInnerText($tdId);
        $split = preg_split("@ +@", $idText);
        $splitAgain = explode('-', $split[0]);

        $card->cid = intval($splitAgain[1], 10);
        $card->rarity = $split[1];
        if (!$splitAgain[1]) {
            var_dump($idText);
        }
        if ($isChar) {
            $isMale = preg_match('@m|男@i', $split[2]);
            $isFemale = preg_match('@f|女@i', $split[2]);
            $card->setIsMale($isMale);
            $card->setIsFemale($isFemale);
        }

        /**
         * Names (eng and jap)
         **/
        $tdName = $tds1->item(1);
        $a = $tdName->getElementsByTagName('a');
        $nameEng = $this->getInnerText($a->item(0));
        $card->nameEng = $nameEng;
        $innerHtml = $this->getInnerHtml($tdName);
        preg_match('@<br />\s*(.*)\s*</td>$@s', $innerHtml, $matches);
        $nameJap = $matches[1];
        $card->nameJap = $nameJap;

        $tdText = $tds1->item(2);
        $innerHtml = $this->getInnerHtml($tdText);
        if ($isChar) {
            $basicAbilityMap = $this->getBasicAbilityMap();
            $brSplit = explode('<br />', $innerHtml);
            foreach ($brSplit as $val) {
                $val = trim(strip_tags($val, '<br><span><img>'));
                $pattern = '@^<span id="basicAbility">(.*): (.*)</span>@';
                $isBasicAbility = preg_match($pattern, $val, $matches);
                if ($isBasicAbility) {
                    $this->addBasicAbilityToChar($card, $matches[1], $matches[2]);
                }

                $pattern = '@^<span id="basicAbility"><span id="basicAbility">(.*)</span></span>: (.*)@';
                $isBasicAbility = preg_match($pattern, $val, $matches);
                if ($isBasicAbility) {
                    $this->addBasicAbilityToChar($card, $matches[1], $matches[2]);
                }

                $pattern = '@^<span id="specialAbility">(.*): (.*)</span>@';
                $isSpecialAbility = preg_match($pattern, $val, $matches);
                if ($isSpecialAbility) {
                    $cost = new Cost;
                    $cost->fillByOmoshiroiHtml($matches[2]);
                    $card->abilityNames[Card::LANG_EN] = $matches[1];
                    $card->abilityCostObj = $cost;
                }

            }
        }


        \Zend\Debug\Debug::dump($card);

    }

    public function addBasicAbilityToChar(Char $card, $basicAbilityTextName, $costText) {
        $map = $this->getBasicAbilityMap();
        $key = strtolower($basicAbilityTextName);
        $enumVal = $map[$key];
        if (is_int($enumVal)) {
            $cost = new Cost;
            $cost->fillByOmoshiroiHtml($costText);
            $card->setBasicAbility($enumVal, true, $cost);
        }
        else {
            $msg = sprintf("Basic ability not found. Text: %s\nCard name: %s\n", $basicAbilityTextName, $card->nameEng);
            trigger_error($msg);
        }
    }

    public function getBasicAbilityMap() {
        static $map = array (
            'dash'          => Char::DASH,
            'aggressive'    => Char::AGGRESSIVE,
            'step'          => Char::STEP,
            'side step'     => Char::SIDE_STEP,
            'order step'    => Char::ORDER_STEP,
            'jump'          => Char::JUMP,
            'escape'        => Char::ESCAPE,
            'side attack'   => Char::SIDE_ATTACK,
            'tax trash'     => Char::TAX_TRASH,
            'tax wakeup'    => Char::TAX_WAKEUP,
            'supporter'     => Char::SUPPORTER,
            'touch'         => Char::TOUCH,
            'attacker'      => Char::ATTACKER,
            'defender'      => Char::DEFENDER,
            'bonus'         => Char::BONUS,
            'penalty'       => Char::PENALTY,
            'deck bonus'    => Char::DECK_BONUS,
        );
        return $map;
    }

    public function getInnerHtml(\DomElement $el) {
        return trim($el->ownerDocument->saveXML($el));
    }

    public function getInnerText(\DomElement $el) {
        return strip_tags($this->getInnerHtml($el));
    }
}

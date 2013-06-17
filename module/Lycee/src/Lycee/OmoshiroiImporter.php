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
            '#0099ff', '#83F290'
        );
        $goodBgColors = array (
            '#ccccff', '#eef3ff'
        );
        $lastBgColor = '#eef3ff';
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
        if (Card::CHAR == $cardType) {
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
        var_dump($card);
    }
}

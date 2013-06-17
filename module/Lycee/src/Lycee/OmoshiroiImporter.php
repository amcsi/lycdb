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
                    if (!in_array($bgColor, $badBgColors)) {
                        $pair[] = $tr;
                        if ($lastBgColor == $bgColor) {
                            if (count($pair) < 2) {
                                $text = sprintf("Couldn't make tr pairs.\nLine no.: %d\nContents: %s\n",
                                    $tr->getLineNo(), $tr->ownerDocument->saveXML($tr)
                                );
                            }
                            $cardList[] = $this->cardBy2TrsInAbilityView($pair[0], $pair[1], $firstTdBgColor);
                            $pair = array ();
                        }
                        else {
                            $firstTdBgColor = $bgColor;
                        }
                    }
                    break;
                }
            }
        }

    }

    public function cardBy2TrsInAbilityView(\DomElement $tr1, \DomElement $tr2, $firstBgColor) {
        static $map = array (
            '#ccccff' => Card::CHAR,
            '#ffcccc' => Card::EVENT,
            '#eeee99' => Card::ITEM,
            '#99ee99' => Card::AREA,
        );
        if (Card::CHAR == $map[$firstBgColor]) {
            $card = new Char();
        }
        else if (Card::EVENT == $map[$firstBgColor]) {
            $card = new Event();
        }
        else if (Card::ITEM == $map[$firstBgColor]) {
            $card = new Item();
        }
        else if (Card::AREA == $map[$firstBgColor]) {
            $card = new Area();
        }
        else {
            trigger_error("Invalid bgColor: $firstBgColor");;
        }
        var_dump($card);
    }
}

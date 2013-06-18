<?php
namespace Lycee;

class Cost extends Lycee {

    protected $cost = 0;
    protected $isTap;
    protected $isAuto;
    public $text;

    public $costSizes = 4;
    
    public function isAuto() {
        return $this->isAuto;
    }
    
    public function isTap() {
        return $this->isTap;
    }
    
    public function getCostElement($element) {
        $amount = $this->getBits($this->cost, $this->costSizes * $element, $this->costSizes);
        return $amount;
    }
    
    public function insertCostElement($amount,$element) {
        try {
            $this->checkCostValue($amount);
            $this->checkElement($element);
        }   catch(MyException $e) {
            throw ($e);
        }
    
    
        
        $integer = Bw::changeBits($this->cost, $element * $this->costSizes, $this->costSizes, $amount);
        $this->cost = $integer;
    }

    public function getOmoshiroiMap() {
        static $ret = array (
            '<img src="elements/0c.gif" />' => 'free',
            '<img src="elements/tp.gif" />' => 'tap',
            '<img src="elements/ew.gif" />' => Lycee::SNOW,
            '<img src="elements/em.gif" />' => Lycee::MOON,
            '<img src="elements/ef.gif" />' => Lycee::FLOWER,
            '<img src="elements/ek.gif" />' => Lycee::LIGHTNING,
            '<img src="elements/es.gif" />' => Lycee::SUN,
            '<img src="elements/er.gif" />' => Lycee::STAR,
        );
        return $ret;
    }
    
    /**
     * Creates a cost object by an HTML snippet from omoshiroi.info
     * 
     * @param string $html 
     * @static
     * @access public
     * @return self
     */
    public function fillByOmoshiroiHtml($html) {
        $map = $this->getOmoshiroiMap();
        $auto = true;
        foreach ($map as $img => $val) {
            $count = substr_count($html, $img);
            if ($count) {
                $auto = false;
                if ('free' == $val) {
                    // the cost is free
                    return;
                }
                else if (is_int($val)) {
                    $this->insertCostElement($count, $val);
                }
                else if ('tap' == $val) {
                    $this->isTap = true;
                }
            }
            $html = str_replace($img, '', $html);
        }
        $this->isAuto = true;
        $trimmed = trim(strip_tags($html), " \t\n\r\0\x0B,");
        if ($trimmed) {
            $this->text = $trimmed;
        }
    }
    
}
?>

<?php
class Cost extends Lycee {

    protected $costElement;
    protected $isTap;
    protected $isAuto;
    protected $texts;
    
    
    public function isAuto() {
        return $this->isAuto;
    }
    
    public function isTap() {
        return $this->isTap;
    }
    
    public function getCostElement($element) {
        $amount = $this->getBits($this->cost, costSizes * $element, costSizes);
        return $amount;
    }
    
    
    
    public function insertCostElement($amount,$element) {
        try {
            $this->checkCostValue($amount);
            $this->checkElement($element);
        }   catch(MyException $e) {
            throw ($e);
        }
    
    
        
        $integer = Bw::changeBits($this->cost,$element * costSizes,costSizes,$amount);
        $this->cost = $integer;
    }
    
    
}
?>

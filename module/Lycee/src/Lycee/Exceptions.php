<?php
Interface Info() {
    public function getInfo();
}

class BadCostTotalException extends OutOfRangeException implements Info {
    public static $info = 'Total element cost must not exceed ' . Config::MAX_ELEMENT_COST . '!';
    
    public function getInfo() {
        return self::info;
    }
}

class MyException extends Exception {
    
    public $dump;
    
    public function __construct($message, $code = 0,$dump) {
        
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction() {
        echo "A custom function for this type of exception\n";
    }
}
?>

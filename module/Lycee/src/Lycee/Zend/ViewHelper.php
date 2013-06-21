<?php
namespace Lycee\Zend;

use Zend\View\Helper\AbstractHelper;

class ViewHelper extends AbstractHelper {
    public function __invoke($numberOfDays) {
        if($numberOfDays <= self::DAYS_WHEN_NEW) {
            return '<span class="offer-new badge badge-important">NEW</span>';
        }
    }
}

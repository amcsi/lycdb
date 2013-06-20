<?php
namespace Lycee;

class Area extends Card {

    public function toDbData() {
        $data = parent::toDbData();
        $data['type'] = self::AREA;
        return $data;
    }
}
?>

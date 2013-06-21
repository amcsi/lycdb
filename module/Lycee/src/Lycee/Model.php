<?php
namespace Lycee;

class Model {
    protected $_amysql;
    protected $_sm;

    public $setsTableName = 'lycdb_sets';
    public $cardsTableName = 'lycdb_cards';

    public $foundRows;

    public function __construct() {
    }

    public function get($options = array ()) {
        $amysql = $this->amysql;

        $limit = ' LIMIT 50 ';
        $offset = ' OFFSET 0 ';
        $wheres = array ();
        $binds = array ();
        $wheres[] = 'ex <= :ex';
        $binds['ex'] = 2;
        $where = $wheres ? ' WHERE ' . join (' AND ', $wheres) : '';

        if (!empty($options['name'])) {
            $expr = $amysql->expr(\AMysql_Expr::ESCAPE_LIKE, $options['name']);
            $wheres[] = 'name_jp LIKE :name OR name_en LIKE :name';
            $binds['name'] = $options['name'];
        }

        if (isset($options['type']) && -2 < ($type = $options['type'])) {
            /**
             * Not a character 
             */
            if (-1 == $type) {
                $wheres[] = "type != :type";
                $binds['type'] = Card::CHAR;
            }
            else {
                $wheres[] = "type = :type";
                $binds['type'] = $type;
            }
        }
        $elements = array ('snow', 'moon', 'flower', 'lightning', 'sun', 'star');
        if (!empty($options['cost'])) {
            $costType = isset($options['cost_type']) ? $options['cost_type'] : 1;
            // exact cost
            if (2 == $costType) {
                foreach ($elements as $key => $element) {
                    $wheres[] = "cost_$element = :cost_$element";
                    $binds["cost_$element"] = isset($cost[$key]) ? $cost[$key] : 0;
                }
            }
            // payable by
            else {
                $total = 0;
                $starWheres = array ();
                foreach ($elements as $key => $element) {
                    $costAmount = isset($options['cost'][$key]) ? $options['cost'][$key] : 0;
                    $total += $costAmount;
                    $starWheres[] = "cost_$element";
                    if ($key != Lycee::STAR) {
                        $wheres[] = "cost_$element <= :cost_$element";
                        $binds["cost_$element"] = $costAmount;
                    }
                    else {
                        $wheres[] = join (' + ', $starWheres) . ' <= :cost_total';
                        $binds['cost_total'] = $total;
                    }
                }
            }
        }

        if (isset($options['ex'], $options['ex_equality'])) {
            $eq = $options['ex_equality'];
            $op = '=';
            if (0 < $eq) {
                $op = '<=';
            }
            else if ($eq < 0) {
                $op = '>=';
            }
            $wheres[] = "ex $op :ex";
            $binds['ex'] = $options['ex'];
        }

        if (isset($options['element'])) {
            $elementType = isset($options['element_type']) ? $options['element_type'] : 1;
            // is
            if (2 == $elementType) {
                foreach ($elements as $key => $element) {
                    if (Lycee::STAR == $key) {
                        continue;
                    }
                    $op = !empty($options['element'][$key]) ? '!=' : '=';
                    $wheres[] = "is_$element $op 0";
                }
            }
            // has
            else {
                foreach ($elements as $key => $element) {
                    if (Lycee::STAR == $key) {
                        continue;
                    }
                    if (!empty($options['element'][$key])) {
                        $wheres[] = "is_$element != 0";
                    }
                }
            }
        }

        if (!empty($options['text'])) {
            $wheres[] = 'ability_desc_jp LIKE :text OR ability_desc_en LIKE :name
                OR comments_jp LIKE :text OR comments_en LIKE :text
            ';
            $expr = $amysql->expr(\AMysql_Expr::ESCAPE_LIKE, $options['text']);
            $binds['text'] = $expr;
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->cardsTableName $where $limit $offset";
        $stmt = $this->amysql->prepare($sql);
        $stmt->execute($binds);
        $result = $stmt->fetchAllAssoc();
        $this->foundRows = $this->amysql->foundRows();

        if (!empty($options['template'])) {
            $positionImgs = array (
            );
            $elements = array ('snow', 'moon', 'flower', 'lightning', 'sun', 'star');
            foreach ($result as &$row) {
                $displayCost = '';
                $displayElements = '';
                foreach ($elements as $element) {
                    if ($row["cost_$element"]) {
                        $displayCost .= str_repeat("[$element]", $row["cost_$element"]);
                    }
                    if (!empty($row["is_$element"])) {
                        $displayElements .= "[$element]";
                    }
                }
                if (!$displayElements) {
                    $displayElements = '[star]';
                }
                $row['cost_markup'] = $displayCost;
                $row['elements_markup'] = $displayElements;
                $row['position_markup'] = '';
                if (Card::CHAR == $row['type']) {
                    $pm = '';
                    $pm .= Char::AL_FLAG & $row['position_flags'] ? '[on]' : '[off]';
                    $pm .= Char::AC_FLAG & $row['position_flags'] ? '[on]' : '[off]';
                    $pm .= Char::AR_FLAG & $row['position_flags'] ? '[on]' : '[off]';
                    $pm .= "\n";
                    $pm .= Char::DL_FLAG & $row['position_flags'] ? '[on]' : '[off]';
                    $pm .= Char::DC_FLAG & $row['position_flags'] ? '[on]' : '[off]';
                    $pm .= Char::DR_FLAG & $row['position_flags'] ? '[on]' : '[off]';
                    $row['position_markup'] = $pm;
                }
                $tt = '';
                switch ($row['type']) {
                    case Card::CHAR:
                        $tt = 'character';
                        break;
                    case Card::AREA:
                        $tt = 'area';
                        break;
                    case Card::EVENT:
                        $tt = 'event';
                        break;
                    case Card::ITEM:
                        $tt = 'item';
                        break;
                    default:
                        $tt = 'unknown';
                        break;
                }
                $row['type_text'] = $tt;
                $row['default_image_external'] = str_replace('-', '_', strtolower($row['cid'])) . '_l.jpg';
                $row['sets_string'] = 'Coming soon...';
            }
        }
        return $result;
    }

    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager) {
        $this->_sm = $serviceManager;
    }

    public function getAMysql() {
        if (!$this->_amysql) {
            $this->_amysql = $this->_sm->get('amysql');
        }
        return $this->_amysql;
    }

    public function __get($key) {
        switch ($key) {
        case 'amysql':
            return $this->getAMysql();
        default:
            throw new Exception ("Bad property name: $key");
        }
    }
    
}
?>

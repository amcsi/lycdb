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
        $limit = ' LIMIT 50 ';
        $offset = ' OFFSET 0 ';
        $wheres = array ();
        $binds = array ();
        $wheres[] = 'ex <= :ex';
        $binds['ex'] = 2;
        $where = $wheres ? ' WHERE ' . join (' AND ', $wheres) : '';


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

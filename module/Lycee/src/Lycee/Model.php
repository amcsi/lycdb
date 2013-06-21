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

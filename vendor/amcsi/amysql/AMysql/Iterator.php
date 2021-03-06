<?php /* vim: set tabstop=8 expandtab : */
/**
 * AMysql_Statement's iterator class.
 *
 * Visit https://github.com/amcsi/amysql
 * @author Szerémi Attila
 * @license     MIT License; http://www.opensource.org/licenses/mit-license.php
 * @version 0.9.3
 **/
class AMysql_Iterator implements SeekableIterator
{
    
    protected $_stmt;
    protected $_count;
    protected $_lastFetch;
    protected $_currentIndex = 0;
    protected $_resultIndex = 0;

    public function __construct(AMysql_Statement $stmt) {
	if (!is_resource($stmt->result)) {
	    throw new LogicException("Statement is not a SELECT statement. ".
		"Unable to iterate. Query: " . $stmt->query);
	}
	$count = $stmt->numRows();
	$this->_stmt = $stmt;
	$this->_count = $count;
    }

    public function current() {
	if ($this->_resultIndex == $this->_currentIndex + 1) {
	    return $this->_lastFetch;
	}
	$ret = $this->_stmt->fetch();
	$this->_resultIndex++;
	$this->_lastFetch = $ret;
	return $ret;
    }

    public function key() {
	return $this->_currentIndex;
    }

    public function next() {
	$this->_currentIndex++;
    }

    public function rewind() {
	if ($this->_count) {
	    $this->seek(0);
	}
    }

    public function valid() {
	if (0 <= $this->_currentIndex && $this->_currentIndex < $this->_count) {
	    return true;
	}
	return false;
    }

    public function seek($index) {
	if (0 <= $index && $index < $this->_count) {
	    mysql_data_seek($this->_stmt->result, $index);
	    $this->_resultIndex = $index;
	    $this->_currentIndex = $index;
	}
	else {
	    throw new OutOfBoundsException("Cannot seek to position `$index`.");
	}
    }
}

<?php
namespace Lycee;
/**
 * Class for importing files from http://lycee-tcg.com/
 **/

class LyceeImporter {

    protected $_cache;
    protected $_serviceManager;

    public function __construct() {
    }

    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager) {
        $this->_serviceManager = $serviceManager;
    }

    public function getCache() {
        if (!$this->_cache) {
            $zendCache = $this->_serviceManager->get('Lycee\Cache');
            $cacheHelper = new Zend\CacheHelper($zendCache);
            $this->_cache = $cacheHelper;
        }
        return $this->_cache;
    }

    public function request($url, $params = array (), $options = array ()) {

    }

    protected function _request($url, $params = array (), $options = array ()) {


    }
}

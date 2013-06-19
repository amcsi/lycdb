<?php
namespace Lycee;
/**
 * Class for importing files from http://lycee-tcg.com/
 **/

class LyceeImporter {

    public $baseUrl = 'http://lycee-tcg.com/card_list';

    /**
     * \Lycee\Zend\CacheHelper
     * 
     * @var mixed
     * @access protected
     */
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

    public function import() {
        return $this->getIndexHtml();
    }

    /**
     * Requests the index page. This is where we should find the card list.
     * Should be cached per day.
     * 
     * @access public
     * @return void
     */
    public function getIndexHtml() {
        $page = 'index.cgi';
        $indexHtml = $this->request($page);
        return $indexHtml;
    }

    public function request($url, $params = array (), $options = array ()) {
        $fullUrl = "$this->baseUrl/$url";
        return $this->requestFullUrl($fullUrl, $params, $options);
    }

    public function requestFullUrl($url, $params = array (), $options = array ()) {
        $cache = $this->getCache();
        $useCache = !isset($options['use_cache']) || empty($options['use_cache']); // use cache by default.
        if (!$useCache || !($result = $cache->getCachedResult($url, $params, $options))) {
            $args = (array) $params;
            $result = $this->_requestFullUrl($url, $args, $options);
            if ($result) {
                $cache->cacheResult($result, $url, $params, $options);
            }
		}
        return $result;
        
    }

    protected function _requestFullUrl($url, $params = array (), $options = array ()) {
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
		$result = file_get_contents($url);
		return $result;
    }
}

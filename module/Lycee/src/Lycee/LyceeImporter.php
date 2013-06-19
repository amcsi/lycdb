<?php
namespace Lycee;
/**
 * Class for importing files from http://lycee-tcg.com/
 *
 * S_L - which filter menu should be open by default
 *      1: sets
 * page_out - How many results to display per page
 * page_list - Style to show results.
 *      1: thumbnail view
 *      2: list view (with all needed details except image)
 * 
 **/

class LyceeImporter {

    public $baseUrl = 'http://lycee-tcg.com/card_list';
    public $convertToUtf8 = true;
    public $websiteVersionTag = "website-version-1";

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
        $sets = $this->getSets();
        $this->importSetByArray($sets[0]);
    }

    public function importSetByArray($arr) {
        $parsedUrl = parse_url($arr['page']);
        parse_str($parsedUrl['query'], $qs);
        $qs['page_out'] = 500;
        $qs['page_list'] = 2;
        $options = array ();
        $options['lifetime'] = 60 * 60 * 24 * 265 * 5; // 5 years.
        $options['cache_tags'] = array ($this->websiteVersionTag);
        $html = $this->request($parsedUrl['path'], $qs, $options);
        echo $html;
        exit;
    }

    public function getSets() {
        $key = 'setsArray';
        $cache = $this->getCache();
        $sets = $cache->getCachedResult($key);
        if (!$sets) {
            $indexHtml = $this->getIndexHtmlWithSetsOpen();
            $domQuery = new \Zend\Dom\Query();
            $domQuery->setDocumentHtml($indexHtml, 'utf-8');
            $setEls = $domQuery->execute('#card_list_main div.m_14a div.m_14b_y div.m_14e a');
            $ret = array ();
            foreach ($setEls as $el) {
                $set['page'] = $el->getAttribute('href');
                $text = trim($el->textContent);
                $pattern = '@^(.*)（(\d+)）$@'; // note the japanese parentheses characters
                preg_match($pattern, $text, $matches);
                $set['name'] = $matches[1];
                $set['count'] = $matches[2];
                $ret[] = $set;
            }
            $cache->cacheResult($sets, $key);
        }
        return $ret;
    }

    /**
     * Requests the index page. This is where we should find the card list.
     * Should be cached per day.
     * 
     * @access public
     * @return void
     */
    public function getIndexHtmlWithSetsOpen() {
        $page = 'index.cgi';
        $params = array ('S_L' => 1);
        $indexHtml = $this->request($page, $params);
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
        $convertToUtf8 = $this->convertToUtf8;
        if (isset($options['convertToUtf8'])) {
            $convertToUtf8 = $options['convertToUtf8'];
        }
        if ($convertToUtf8) {
            $result = mb_convert_encoding($result, 'utf-8', array ('EUC_JP'));
            $result = str_replace('charset=EUC-JP', 'charset=UTF-8', $result);
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

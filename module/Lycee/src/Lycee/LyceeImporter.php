<?php
namespace Lycee;
/**
 * Class for importing files from http://lycee-tcg.com/
 **/

class LyceeImporter {

    protected $_cache;

    public function __construct() {
        echo "instantiated lycee importer (should be after 'before invoke' if lazy loading)<br>\n";
    }

    public function setCache(\Zend\Cache\Storage\Adapter\AbstractAdapter $cache) {

    }

    public function request($url, $params = array (), $options = array ()) {

    }

    protected function _request($url, $params = array (), $options = array ()) {


    }

    public function getCachedResult($method, $params = array (), $options = array ()) {
        $cache = $this->getCacheAdapter();
        $cacheKey = $this->getCacheKey($method, $params, $options);
        return $cache->load($cacheKey);
    }

    public function cacheResult($result, $method, $params = array (), $options = array ()) {
        $cache = $this->getCacheAdapter();
        $cacheKey = $this->getCacheKey($method, $params, $options);
        $lifetime = !empty($options['lifetime']) ? $options['lifetime'] : false;
		$cacheTags = isset($options['cache_tags']) ? $options['cache_tags'] : array ();
        return $cache->save($result, $cacheKey, $cacheTags, $lifetime);
    }

    public function getCacheKey($method, $params = array (), $options = array ()) {
        $cacheKey = !empty ($options['cache_key']) ?
            $options['cache_key'] :
            sha1($method . serialize($params) . serialize($options));
        return $cacheKey;
    }

    public function clearCachedResult($result, $params = array (), $options = array ()) {
        $cache = $this->getCacheAdapter();
        $cacheKey = $this->getCacheKey($result, $params, $options);
        $cache->clear($cacheKey);
    }
}

<?php
namespace Lycee\Zend;

class CacheHelper {

    protected $_cache;
    protected $_cacheOptions;
    protected $_defaultTtl;

    public function __construct(\Zend\Cache\Storage\Adapter\AbstractAdapter $cache) {
        $this->_cache = $cache;
        $this->_cacheOptions = $cache->getOptions();
        $this->_defaultTtl = $this->_cacheOptions->getTtl();
    }

    public function getCachedResult($method, $params = array (), $options = array ()) {
        $cache = $this->_cache();
        $cacheKey = $this->getCacheKey($method, $params, $options);
        return $cache->getItem($cacheKey);
    }

    public function cacheResult($result, $method, $params = array (), $options = array ()) {
        $cache = $this->_cache();
        $cacheKey = $this->getCacheKey($method, $params, $options);
		$cacheTags = isset($options['cache_tags']) ? $options['cache_tags'] : array ();
        if (isset($options['lifetime'])) {
            $lifetime = !empty($options['lifetime']) ? $options['lifetime'] : false;
            $this->_cacheOptions->setTtl($lifetime);
        }
        $ret = $cache->setItem($cacheKey, $result);
        $cache->setTags($cacheTags);
        if (isset($options['lifetime'])) {
            $this->_cacheOptions->setTtl($this->defaultTtl);
        }
        return $ret;
    }

    public function getCacheKey($method, $params = array (), $options = array ()) {
        $cacheKey = !empty ($options['cache_key']) ?
            $options['cache_key'] :
            sha1($method . serialize($params) . serialize($options));
        return $cacheKey;
    }

    public function clearCachedResult($result, $params = array (), $options = array ()) {
        $cache = $this->_cache();
        $cacheKey = $this->getCacheKey($result, $params, $options);
        $cache->clear($cacheKey);
    }

    public function __call($method, $args) {
        return call_user_func_array(array ('parent', $method), $args);
    }
}
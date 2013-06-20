<?php
namespace Lycee\Zend;

class CacheHelper {

    /**
     * \Zend\Cache\Storage\Adapter\AbstractAdapter
     * 
     * @var mixed
     * @access protected
     */
    protected $_cache;
    protected $_cacheOptions;
    protected $_defaultTtl;

    public function __construct(\Zend\Cache\Storage\Adapter\AbstractAdapter $cache) {
        $this->_cache = $cache;
        $this->_cacheOptions = $cache->getOptions();
        $this->_defaultTtl = $this->_cacheOptions->getTtl();
    }

    public function getCachedResult($method, $params = array (), $options = array ()) {
        $cache = $this->_cache;
        $cacheKey = $this->getCacheKey($method, $params, $options);
        if (isset($options['lifetime'])) {
            $this->_cacheOptions->setTtl($options['lifetime']);
        }
        $ret = $cache->getItem($cacheKey);
        if (isset($options['lifetime'])) {
            $this->_cacheOptions->setTtl($this->_defaultTtl);
        }
        return $ret;
    }

    public function cacheResult($result, $method, $params = array (), $options = array ()) {
        $cache = $this->_cache;
        $cacheKey = $this->getCacheKey($method, $params, $options);
		$cacheTags = isset($options['cache_tags']) ? $options['cache_tags'] : array ();
        if (isset($options['lifetime'])) {
            $this->_cacheOptions->setTtl($options['lifetime']);
        }
        $ret = $cache->setItem($cacheKey, $result);
        $cache->setTags($cacheKey, $cacheTags);
        if (isset($options['lifetime'])) {
            $this->_cacheOptions->setTtl($this->_defaultTtl);
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
        $cache = $this->_cache;
        $cacheKey = $this->getCacheKey($result, $params, $options);
        $cache->clear($cacheKey);
    }

    public function __call($method, $args) {
        return call_user_func_array(array ($this->_cache, $method), $args);
    }
}

<?php

abstract class CachingPlugin {

    protected $_useCache = true;

    protected function _setCache($key, $value, $expire = Cache::DEFAULT_EXPIRE) {
        if ($this->_useCache) {
            Cache::set($key, $value, $expire);
        }
    }

    protected function _getCache($key) {
        if ($this->_useCache) {
            return Cache::get($key);
        }
        return null;
    }

    protected function _deleteCache($key) {
        if ($this->_useCache) {
            Cache::delete($key);
        }
    }

    protected function _getCacheKey() {
        return 'cache_' . md5(uniqid('cache_', true));
    }
}
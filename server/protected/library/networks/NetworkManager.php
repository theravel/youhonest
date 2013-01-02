<?php

final class NetworkManager extends CachingPlugin {

    /**
     * @var NetworkManager
     */
    protected static $_instance;
    
    protected $_networks;

    protected function __construct() {
        $this->_loadData();
    }

    public static function getAllNetworks() {
        $result = array();
        foreach (self::_getInstance()->_networks as $network) {
            if ($network->isEnabled()) {
                $result[] = $network;
            }
        }
        return array_reverse($result);
    }

    /**
     * @param int $networkId
     * @return NetworkBase
     */
    public static function getNetworkByID($networkId) {
        if ( isset(self::_getInstance()->_networks[$networkId]) ) {
            $network = self::_getInstance()->_networks[$networkId];
            if ($network->isEnabled()) {
                return $network;
            } else {
                throw new NetworkDisabledException("Network $networkId is disabled");
            }
        }
        throw new NetworkException('Network with such ID does not exist');
    }

    public static function getNetworkByURL($url) {
        foreach (self::_getInstance()->_networks as $network) {
            if (preg_match($network->getUrlPattern(), $url)) {
                if ($network->isEnabled()) {
                    return $network;
                } else {
                    throw new NetworkDisabledException("Network $networkId is disabled");
                }
            }
        }
        throw new NetworkException('Network with such URL does not exist');
    }

    /***** protected *****/
    
    protected function _getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function _getCacheKey() {
        return 'networks_list';
    }

    protected function _loadData() {
        $cacheKey = $this->_getCacheKey();
        $networks = $this->_getCache($cacheKey);
        if (!$networks) {
            $networks = NetworkModel::model()->findAll();
            $this->_setCache($cacheKey, $networks);
        }
        
        foreach ($networks as $network) {
            $className = $network->name . 'Network';
            if (class_exists($className)) {
                $instance = new $className($network);
                $this->_networks[$network->network_id] = $instance;
            } else {
                throw new NetworkException("Class for {$network->name} does not exist");
            }
        }
    }
}
<?php

class TimeUtilities {
   
    protected static $_instance;
    
    protected function __construct() {}

    /**
     * @return TimeUtilities
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function now() {
       return time();
    }
}

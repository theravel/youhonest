<?php

final class Cache {
    
    const DEFAULT_EXPIRE = 3600;

    public static function get($key) {
        return Yii::app()->cache->get($key);
    }

    public static function set($key, $value, $expire = self::DEFAULT_EXPIRE) {
        Yii::app()->cache->set($key, $value, $expire);
    }

    public static function delete($key) {
        Yii::app()->cache->delete($key);
    }

    public static function deleteAll() {
        Yii::app()->cache->flush();
    }
}

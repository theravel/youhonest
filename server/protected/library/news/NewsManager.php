<?php

class NewsManager extends CachingPlugin {

    /**
     * @var NewsManager
     */
    protected static $_instance;

    protected function __construct() {
        $this->_useCache = true;
    }

    /**
     * @return NewsManager
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getNews() {
        $key = $this->_getCacheKey();
        $news = $this->_getCache($key);
        if ($news) {
            return $news;
        }
        $news = NewsModel::model()->findAll();
        $this->_setCache($key, $news, 3600);
        return $news;
    }

    protected function  _getCacheKey() {
        return 'all_news';
    }
}
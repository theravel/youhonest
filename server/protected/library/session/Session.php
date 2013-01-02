<?php

class Session extends CachingPlugin {

    const AUTH_COOKIE_NAME = 'auth_cookie';
    const TOKEN_HEADER = 'HTTP_YOUHONEST_TOKEN';

    const SESSION_EXPIRE = 900;
    const SESSION_PREFIX = 'sess_';

    /**
     * @param int $userId
     * @return value
     */
    public function setCookie($value) {
        $cookie = new CHttpCookie(self::AUTH_COOKIE_NAME, $value);
        $cookie->expire = strtotime('+1 year');
        $cookie->domain = '.' . NetIntegration::EXTERNAL_URL;
        //$cookie->httpOnly = true;
        Yii::app()->request->cookies[self::AUTH_COOKIE_NAME] = $cookie;

        return $value;
    }

    /**
     * @return string
     */
    public function getCookie() {
        // Dirty hack as Opera does not allow me to use Cookies from BG page
        // http://stackoverflow.com/questions/13697496/why-are-cookies-unavailable-from-opera-extension-background-page
        if (isset($_SERVER[self::TOKEN_HEADER])) {
            return $_SERVER[self::TOKEN_HEADER];
        }
        if (Yii::app()->request->cookies->contains(self::AUTH_COOKIE_NAME)) {
            return Yii::app()->request->cookies[self::AUTH_COOKIE_NAME]->value;
        }
        return null;
    }

    public function deleteCookie() {
        if (Yii::app()->request->cookies->contains(self::AUTH_COOKIE_NAME)) {
            $cookie = Yii::app()->request->cookies[self::AUTH_COOKIE_NAME]->value;
            unset(Yii::app()->request->cookies[self::AUTH_COOKIE_NAME]);
            $this->_deleteCache( $this->_getCacheKey($cookie) );
        }
    }

    public function buildSession($sessionId, SessionData $data) {
        $this->setCookie($sessionId);
        $cacheKey = $this->_getCacheKey($sessionId);
        $this->_setCache($cacheKey, serialize($data), self::SESSION_EXPIRE);
    }

    /**
     * @return string
     */
    public function generateSessionId() {
        return md5(rand(10000, 10000000) . self::AUTH_COOKIE_NAME . uniqid(self::AUTH_COOKIE_NAME, true));
    }

    /**
     * @return SessionData
     */
    public function getSessionData($sessionId) {
        if ($sessionId) {
            $cacheKey = $this->_getCacheKey($sessionId);
            $data = $this->_getCache($cacheKey);
            if ($data) {
                return unserialize($data);
            }
        }
        return null;
    }

    protected function _getCacheKey($sessionId) {
        return self::SESSION_PREFIX . $sessionId;
    }


}
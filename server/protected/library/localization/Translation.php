<?php

class Translation extends CachingPlugin {

    const DEFAULT_LANGUAGE = 'ru';
    
    protected static $_instance;

    protected $_language = self::DEFAULT_LANGUAGE;
    
    protected function __construct() {}

    /**
     * @return Translation
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function setLanguage($language) {
        $this->_language = str_replace('_', '-', $language);
    }

    /*
     * Translates key into natural language
     *
     * Examples of usage:
     *     translate('MINUTE', array(), 1)   // returns "minute"
     *     translate('MINUTE', array(), 3)   // returns "minutes"
     *     translate('HELLO_USERNAME', array('NAME' => 'Dmitry'))   // Hello, Dmitry
     *     translate('HELLO_USERNAME', array('NAME' => 'Ekaterina'))   // Hello, Ekaterina
     * 
     * @param string $key
     * @param array $replacemets defaut empty array
     * @param $plurality int
     * @return string
     */
    public function translate($key, array $replacements = array(), $plurality = 1) {
        $language = $this->_language;
        $value = $this->_getValueByLanguage($language, $key, $plurality);
        // fallback in parent languages
        if (null === $value && $language !== self::DEFAULT_LANGUAGE) {
            $langParts = explode('-', $language);
            if (2 === count($langParts)) {
                $language = $langParts[0];
                $value = $this->_getValueByLanguage($language, $key, $plurality);
            }

            // fallback in default language
            if (null === $value && $language !== self::DEFAULT_LANGUAGE) {
                $value = $this->_getValueByLanguage(self::DEFAULT_LANGUAGE, $key, $plurality);
            }
        }
        if (null === $value) {
            // mark untranslated strings
            return '__' . $key . '__';
        }
        return $this->_replace($value, $replacements);
    }

    /**** protected *****/
    protected function _getCacheKey($language, $key, $plurality) {
        return $language . '_' . $key . '_' . $plurality;
    }

    protected function _getValueByLanguage($language, $key, $plurality) {
        $cacheKey = $this->_getCacheKey($language, $key, $plurality);
        $cachedValue = $this->_getCache($cacheKey);
        if ($cachedValue) {
            return $cachedValue;
        }
        $value = TranslationModel::model()->find(array(
            'select' => '`value`',
            'condition' => '
                `key` = :key AND
                `language` = :language AND
                `plurality_min` >= :plurality AND
                `plurality_max` >= :plurality',
            'params' => array(
                ':key' => $key,
                ':language' => $language,
                ':plurality' => $plurality,
            ),
        ));
        if (null !== $value) {
            $this->_setCache($cacheKey, $value->value);
            return $value->value;
        }
        return null;
    }

    protected function _replace($value, array $replacements) {
        foreach ($replacements as $key => $replacement) {
            $value = preg_replace('/__' . strtoupper($key) .  '__/', $replacement, $value);
        }
        return $value;
    }
}
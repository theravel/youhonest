<?php

class NetIntegration {

   const EXTERNAL_URL = 'youhonest.com';
   
   const CONTENT_HTML = 'html';
   const CONTENT_JSON = 'json';
   
   /**
    * Does simple HTTP call and return response
    * 
    * @param string $url
    * @param array $params for example contentType
    * @return string|object
    * @throws NetIntegrationException 
    */
   public static function makeHTTPCall($url, array $params = array()) {
        $contentType = isset($params['contentType']) ? $params['contentType'] : self::CONTENT_JSON;
        if ($response = @file_get_contents($url)) {
            switch ($contentType) {
                case self::CONTENT_JSON:
                    return json_decode($response);
                case self::CONTENT_HTML:
                    return $response;
            }
            return $response;
        }
        throw new NetIntegrationException("Url '$url' is not accessible");
    }
    
}
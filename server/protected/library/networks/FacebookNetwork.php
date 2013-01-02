<?php

class FacebookNetwork extends NetworkBase {

    const APP_ID = '341821995910512';
    const APP_SECRET = '815212bc2f5973727f52eb355a1a6087';

    protected $_authURL = 'https://graph.facebook.com/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s';
    protected $_getTokenURL = 'https://graph.facebook.com/oauth/access_token?client_id=%s&client_secret=%s&&redirect_uri=%s&code=%s';
    protected $_getInfoURL = 'https://graph.facebook.com/me?fields=%s&access_token=%s';

    protected $_token;
    protected $_fbID;

    public function getAuthorizationURL() {
        $settings = array('');
        return sprintf(
            $this->_authURL,
            self::APP_ID,
            implode(',', $settings),
            $this->_redirectURL
        );
    }

    public function getNetworkName() {
        return 'Facebook';
    }

    public function getAuthorizationInfoByNetworkUserId($userId) {
        $authorizationModel = $this->_getAuthorizationModel(false);
        $info = $authorizationModel->find(array(
            'condition' => '`fb_user_id` = :userId',
            'params' => array(
                ':userId' => $userId,
            ),
        ));
        if ($info !== null) {
            return $info->getAttributes();
        }
        return null;
    }

    public function getAuthorizationInfoByUserId($userId) {
        $authorizationModel = $this->_getAuthorizationModel(false);
        $info = $authorizationModel->find(array(
            'condition' => '`user_id` = :userId',
            'params' => array(
                ':userId' => $userId,
            ),
        ));
        if ($info !== null) {
            return $info->getAttributes();
        }
        return null;
    }

    public function checkAuthorizationExists() {
        $code = Yii::app()->request->getQuery('code');
        $tokenURL = sprintf(
            $this->_getTokenURL,
            self::APP_ID,
            self::APP_SECRET,
            $this->_redirectURL,
            $code
        );
        
        $authData = NetIntegration::makeHTTPCall($tokenURL, array(
            'contentType' => NetIntegration::CONTENT_HTML,
        ));
        preg_match('/access_token=(?<token>.+?)&expires=.+/', $authData, $matches);
        $this->_token = $matches['token'];

        $userData = $this->_getUserData();
        $this->_fbID = $userData->id;

        return $this->getAuthorizationInfoByNetworkUserId($this->_fbID);
    }

    public function createAuthorization($userId) {
        $userData = $this->_getUserData();

        $authorizationModel = $this->_getAuthorizationModel();
        $authorizationModel->user_id = $userId;
        $authorizationModel->fb_user_id = $userData->id;
        $authorizationModel->first_name = $userData->first_name;
        $authorizationModel->last_name = $userData->last_name;
        $authorizationModel->photo_url = $userData->picture;
        $authorizationModel->token = $this->_token;
        $authorizationModel->locale = $userData->locale;

        $authorizationModel->create_date = TimeUtilities::getInstance()->now();
        $authorizationModel->update_date = TimeUtilities::getInstance()->now();
        $authorizationModel->token_update_date = TimeUtilities::getInstance()->now();
        
        $authorizationModel->save();

        $this->_authID = $authorizationModel->getPrimaryKey();
    }

    public function updateAuthorization() {
        ; // @TODO
    }

    public function updateAuthorizationsPortion() {
        ; // @TODO
    }
    
    public function setDataFromSession(array $data) {
        $this->_authID = $data['authorization_id'];
        $this->_fbID = $data['fb_user_id'];
        $this->_token = $data['token'];
    }

    public function checkPostIdValid($postId) {
        return true;
    }

    protected function _getExternalIdFieldName() {
        return 'fb_user_id';
    }

    protected function _getUserData() {
        $fields = array('first_name', 'last_name', 'picture', 'locale');
        $infoURL = sprintf(
            $this->_getInfoURL,
            implode(',', $fields),
            $this->_token
        );
        $userData = NetIntegration::makeHTTPCall($infoURL);
        $userData->picture = $userData->picture->data->url;
        return $userData;
    }

}
<?php

class VkontakteNetwork extends NetworkBase {

    const APP_ID = 3095745;
    const APP_SECRET = '1Ga7AOw4hvVPrKK1ewRu';
    
    protected $_dislikesLimit = 6;
    protected $_extendedDislikesLimit = 24;
    protected $_tokenDuration = 7776000;  // 3 months
    protected $_authorizationsUpdatePortion = 100;
    protected $_minAuthorizationsUpdateInterval = 10800;  // 3 hours

    protected $_authURL = 'http://oauth.vk.com/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s&response_type=code';
    protected $_getTokenURL = 'https://api.vkontakte.ru/oauth/access_token?client_id=%s&client_secret=%s&code=%s';
    protected $_getInfoURL = 'https://api.vkontakte.ru/method/getProfiles?uids=%s&access_token=%s&fields=%s';
    
    protected $_token;
    protected $_vkID;


    public function getAuthorizationURL() {
        $settings = array('offline');
        return sprintf(
            $this->_authURL,
            self::APP_ID,
            implode(',', $settings),
            urlencode($this->_redirectURL)
        );
    }

    public function getNetworkName() {
        return Translation::getInstance()->translate('NETWORKS::VKONTAKTE');
    }

    public function getAuthorizationInfoByNetworkUserId($userId) {
        $authorizationModel = $this->_getAuthorizationModel(false);
        $info = $authorizationModel->find(array(
            'condition' => '`vk_user_id` = :userId',
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
            $code
        );

        $authData = NetIntegration::makeHTTPCall($tokenURL);
        $this->_token = $authData->access_token;
        $this->_vkID = $authData->user_id;

        return $this->getAuthorizationInfoByNetworkUserId($this->_vkID);
    }

    public function createAuthorization($userId) {
        $userData = $this->_getUserData(array($this->_vkID));

        $authorizationModel = $this->_getAuthorizationModel();
        $authorizationModel->user_id = $userId;
        $authorizationModel->vk_user_id = $userData->uid;
        $authorizationModel->first_name = $userData->first_name;
        $authorizationModel->last_name = $userData->last_name;
        $authorizationModel->photo_url = $userData->photo;
        $authorizationModel->token = $this->_token;

        $authorizationModel->create_date = TimeUtilities::getInstance()->now();
        $authorizationModel->update_date = TimeUtilities::getInstance()->now();
        $authorizationModel->token_update_date = TimeUtilities::getInstance()->now();

        $authorizationModel->save();

        $this->_authID = $authorizationModel->getPrimaryKey();
    }

    public function updateAuthorization() {
        $authorizationModel = $this->_getAuthorizationModel(false);
        $model = $authorizationModel->find(array(
            'condition' => '`vk_user_id` = :userId',
            'params' => array(
                ':userId' => $this->_vkID,
            ),
        ));
        if (null === $model) {
            throw new NetworkException('Cannot update authorization that does not exist');
        }        
        
        $userData = $this->_getUserData(array($this->_vkID));

        $model->first_name = $userData->first_name;
        $model->last_name = $userData->last_name;
        $model->photo_url = $userData->photo;
        $model->token = $this->_token;
        $model->update_date = TimeUtilities::getInstance()->now();
        $model->token_update_date = TimeUtilities::getInstance()->now();
        $model->expired = 0;

        $model->save();

        $this->_authID = $model->authorization_id;
        return $model->authorization_id;
    }

    public function setDataFromSession(array $data) {
        $this->_authID = $data['authorization_id'];
        $this->_vkID = $data['vk_user_id'];
        $this->_token = $data['token'];
    }

    public function updateAuthorizationsPortion() {
        $authorizations = $this->_getAuthorizationPortionForUpdate();
        if (empty($authorizations)) {
            // no updates needed
            return 0;
        }
        
        $userIds = array();
        foreach ($authorizations as $authorization) {
            $userIds[] = $authorization->vk_user_id;
        }
        $usersCount = count($userIds);
        $usersData = $this->_getUserData($userIds);
        
        $latestValidTokenDate = TimeUtilities::getInstance()->now() - $this->_tokenDuration;
        foreach ($authorizations as $authorization) {
            $userData = $usersCount == 1 ? $usersData : $this->_findUserByIdFromMany($usersData, $authorization->vk_user_id);
            $authorization->first_name = $userData->first_name;
            $authorization->last_name = $userData->last_name;
            $authorization->photo_url = $userData->photo;
            $authorization->update_date = TimeUtilities::getInstance()->now();            
            // check if token has expired or not
            if ($authorization->token_update_date < $latestValidTokenDate) {
                $authorization->expired = 1;
            }
            $authorization->save();
        }
        // updates applied
        return $usersCount;
    }

    public function checkPostIdValid($postId) {
        return preg_match(
            '/^(post-?\d+_\d+)|(video-?\d+_\d+)|(photo-?\d+_\d+)$/',
            $postId
        );
    }

    protected function _getExternalIdFieldName() {
        return 'vk_user_id';
    }

    protected function _getUserName($authorizationInfo) {
        if ($this->_nameFormat == self::NAME_FORMAT_ONLY_NAME) {
            return $authorizationInfo->first_name;
        }
        return $authorizationInfo->first_name . ' ' . $authorizationInfo->last_name;
    }
    
    protected function _getAuthorizationPortionForUpdate() {
        $authModel = $this->_getAuthorizationModel();
        return $authModel->findAll(array(
            'condition' => 'update_date < :date AND expired = 0',
            'params' => array(
                ':date' => TimeUtilities::getInstance()->now() - $this->_minAuthorizationsUpdateInterval,
            ),
            'order' => 'update_date',
            'limit' => $this->_authorizationsUpdatePortion,
        ));
    }
    
    protected function _findUserByIdFromMany(array $usersData, $userId) {
        foreach ($usersData as $userData) {
            if ($userId == $userData->uid) {
                return $userData;
            }
        }
        throw new NetworkException('User with such uid was not found');
    }

    protected function _getUserData(array $userIDs) {
        if (empty($userIDs)) {
            throw new NetworkException('You must specify userIds');
        }
        $fields = 'uid,first_name,last_name,photo';
        $infoURL = sprintf(
            $this->_getInfoURL,
            implode(',', $userIDs),
            $this->_token,
            $fields
        );
        $userData = NetIntegration::makeHTTPCall($infoURL);
        if (!isset($userData->response)) {
            throw new NetworkException('Cannot get profiles');
        }
        if (count($userIDs) === 1) {
            return $userData->response[0];
        } else {
            return $userData->response;
        }
    }

}
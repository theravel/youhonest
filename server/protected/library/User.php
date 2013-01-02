<?php

class User {

    protected $_id;
    protected $_cookie;
    protected $_latestNewsDate;
    protected $_networksEnabled = array(
        'vk_enabled' => 1,
        'fb_enabled' => 1,
        'gplus_enabled' => 1,
    );

    /**
     * @var UserModel
     */
    protected $_model;

    protected function __construct() {
        ;
    }

    public function getUserId() {
        return $this->_id;
    }

    public function getCookie() {
        return $this->_cookie;
    }

    public function getConnectedNetworks() {
        ;
    }

    /**
     * @param NetworkBase $network
     * @return int userId (new id or old user id)
     */
    public function addNetworkConnection(NetworkBase $network) {
        if ($authData = $network->checkAuthorizationExists()) {
            if ($authData['expired']) {
                $network->updateAuthorization();
                return;
            }
            throw new InvalidDataException('Such connection already exists for user');
        }
        $network->createAuthorization($this->_id);
    }

    /**
     * @param NetworkBase $network
     * @return User
     */
    public static function createByNetworkConnection(NetworkBase $network) {
        if ($authData = $network->checkAuthorizationExists()) {
            if ($authData['expired']) {
                $network->updateAuthorization();
            }
            return self::createByUserId($authData['user_id']);
        }

        $session = new Session();
        $userModel = new UserModel();
        $userModel->cookie = $session->generateSessionId();
        $userModel->latest_news_date = TimeUtilities::getInstance()->now();
        $userModel->save();

        $user = new self();
        $user->_cookie = $userModel->cookie;
        $user->_latestNewsDate = $userModel->latest_news_date;
        $user->_id = (int)$userModel->getPrimaryKey();
        $network->createAuthorization($user->_id);
        return $user;
    }

    /**
     * @param ind $userId
     * @return User
     */
    public static function createByUserId($userId) {
        $user = new self();
        $data = self::_getDataById($userId);
        return self::_setPropertiesFromData($user, $data);

    }

    /**
     * @param string $cookie
     * @return User
     */
    public static function createByCookie($cookie) {
        $user = new self();
        $data = self::_getDataByCookie($cookie);
        return self::_setPropertiesFromData($user, $data);
    }

    public function disableNetwork(NetworkBase $network) {
        $this->_changeNetworkEnabled($network, false);
    }

    public function enableNetwork(NetworkBase $network) {
        $this->_changeNetworkEnabled($network, true);
    }

    public function isNetworkEnabled(NetworkBase $network) {
        return $this->_networksEnabled[ $network->getUserEnabledField() ];
    }

    /**
     * @return int
     */
    public function getLatestShownNewsDate() {
        return $this->_latestNewsDate;
    }

    public function setLatestShownNewsDate($date) {
        $model = $this->_getDataById($this->_id);
        $model->latest_news_date = $this->_latestNewsDate = $date;
        $model->save();
    }
    
    protected static function _getDataById($userId) {
        $data = UserModel::model()->find(array(
            'condition' => '`user_id` = :userId',
            'params' => array(
                ':userId' => $userId,
            ),
        ));
        if ($data !== null) {
            return $data;
        }
        throw new InvalidDataException('User with such ID does not exist');
    }

    protected static function _getDataByCookie($cookie) {
        $data = UserModel::model()->find(array(
            'condition' => '`cookie` = :cookie',
            'params' => array(
                ':cookie' => $cookie,
            ),
        ));
        if ($data !== null) {
            return $data;
        }
        throw new InvalidDataException('User with such ID does not exist');
    }

    protected static function _setPropertiesFromData(User $user, UserModel $data) {
        $user->_id = $data->user_id;
        $user->_cookie = $data->cookie;
        $user->_latestNewsDate = $data->latest_news_date;
        $user->_networksEnabled = array(
            'vk_enabled' => $data->vk_enabled,
            'fb_enabled' => $data->fb_enabled,
            'gplus_enabled' => $data->gplus_enabled,
        );
        return $user;
    }

    /**
     * @param NetworkBase $network
     * @param bool $enabled
     */
    protected function _changeNetworkEnabled($network, $enabled) {
        $model = $this->_getDataById($this->_id);
        $field = $network->getUserEnabledField();
        $model->$field = (int)$enabled;
        $model->save();

        $this->_networksEnabled[ $field ] = (int)$enabled;
    }
}

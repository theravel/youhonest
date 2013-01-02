<?php

abstract class BaseController extends CController {

    /**
     * @var User
     */
    protected $_user;

    /**
     * @var Translation
     */
    protected $_translation;

    /**
     * @var SessionData
     */
    protected $_session;

    public $viewData;

    public $externalUrl;


    public function init() {
        parent::init();
        $this->layout = false;
        $this->viewData = new stdClass();
        $this->externalUrl = 'http://' . NetIntegration::EXTERNAL_URL;
        
        $this->_handleSession();
        // this will initialize Translation with default language
        $this->_translation = Translation::getInstance();
    }

    protected function beforeAction($action) {
        parent::beforeAction($action);
        if ($this->_user === null) {
            throw new CHttpException(401, 'Not authorized');
        }
        return true;
    }

    public function renderAjax() {
        $this->render('/_ajax');
    }

    public function translate($key, array $replacements = array(), $plurality = 1) {
        return $this->_translation->translate($key, $replacements , $plurality);
    }

    /*
     * Alias for "translate" method for quick access
     */
    public function t($key, array $replacements = array(), $plurality = 1) {
        return $this->_translation->translate($key, $replacements , $plurality);
    }

    protected function _handleSession() {
        $session = new Session();
        $cookie = $session->getCookie();
        if ($cookie === null) {
            $this->_user = null;
            return;
        }
        $this->_session = $session->getSessionData($cookie);
        if ($this->_session !== null) {
            $this->_user = $this->_session->user;
            return;
        }
        try {
            $this->_session = $this->_buildSessionData(null, $cookie);
            $session->buildSession($cookie, $this->_session);
        } catch (InvalidDataException $ex) {
            $session->deleteCookie();
        }
    }

    /**
     * @param User $user
     * @param string $cookie
     * @return SessionData
     */
    protected function _buildSessionData($user, $cookie = null) {
        $data = new SessionData();
        if ($user === null) {
            if ($cookie === null) {
                throw new InvalidDataException('You must specify cookies if user is empty');
            }
            $user = User::createByCookie($cookie);
            $this->_user = $user;
        }
        $networks = NetworkManager::getAllNetworks();
        foreach ($networks as $network) {
            $authorization = $network->getAuthorizationInfoByUserId($user->getUserId());
            if (null !== $authorization && !$authorization['expired']) {
                $data->networksInfo[$network->getNetworkId()] = $authorization;
            } else {
                $data->networksInfo[$network->getNetworkId()] = null;
            }
        }
        $data->user = $user;
        return $data;
    }

    protected function _rebuildSession() {
        $session = new Session();
        $session->buildSession($this->_user->getCookie(), $this->_session);
    }

    /**
     * Get network by id and handle situation if network is disabled
     * @param int $networkId
     * @throws NetworkException
     * @return NetworkBase
     */
    protected function _getNetworkById($networkId) {
        try {
            return NetworkManager::getNetworkByID($networkId);
        } catch (NetworkDisabledException $e) {
            throw new CHttpException(405, $e->getMessage());
        }
    }

    /**
     * Initializes network by networkId using user data from session
     * @param int $networkId
     * @throws NetworkException if network is disabled in settings or user is not autorized for it
     * @return NetworkBase
     */
    protected function _initNetwork($networkId) {
        $network = $this->_getNetworkById($networkId);

        if (!$this->_session->user->isNetworkEnabled($network)) {
            throw new CHttpException(405, 'This network is disabled by user');
        }
        if (isset($this->_session->networksInfo[$networkId])) {
            $networkData = $this->_session->networksInfo[$networkId];
            $network->setDataFromSession($networkData);
            return $network;
        } else {
            throw new CHttpException(401, 'Not authorized for this network');
        }
    }

}

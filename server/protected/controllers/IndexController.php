<?php

class IndexController extends BaseController {

    protected function beforeAction($action) {
        return true;
    }

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
        if ($this->_user === null) {
            return $this->forward('login');
        }
		$this->render('index');
	}

    public function actionLogout() {
        $session = new Session();
        $session->deleteCookie();
        $this->render('logout');
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
	    if (!APP_ENV_PRODUCTION && $error = Yii::app()->errorHandler->error) {
	        $this->viewData->error = $error;
           $this->renderAjax();
	    }
	}

    /**
     * Action is a callback for social networks when user attempts to authentificate using this network
     */
    public function actionReturn() {
        $networkId = Yii::app()->request->getQuery('network_id');
        $network = $this->_getNetworkById($networkId);
        if ($this->_user === null) {
            $this->_user = User::createByNetworkConnection($network);
        } else {
            $this->_user->addNetworkConnection($network);
        }
        
        $session = new Session();
        $this->_session = $this->_buildSessionData($this->_user);
        $session->buildSession($this->_user->getCookie(), $this->_session);

        $data = array(
            'url' => $network->getUrl(),
        );
        $this->render('return', $data);
    }

	public function actionLogin() {
        $networkId = YII::app()->request->getPost('networkId');
        $language = YII::app()->request->getPost('language');
        $this->_translation->setLanguage($language);
        $network = $this->_getNetworkById($networkId);
        $data = array(
            'network' => $network,
        );
        $this->render('login', $data);
	}

    public function actionNetworks() {
        $language = YII::app()->request->getPost('language');
        $this->_translation->setLanguage($language);
        
        $this->viewData->networks = array();

        $networks = NetworkManager::getAllNetworks();
        foreach ($networks as $network) {
            $this->viewData->networks[] = array(
                'id' => $network->getNetworkId(),
                'name' => $network->getNetworkName(),
            );
        }
        $this->renderAjax();
    }
}
<?php

class CronController extends BaseController {

    protected function beforeAction($action) {
        $this->_assertAccessKey();
		return true;
	}

    public function actionUpdateAuthorizations() {
        $data = array(
            'updated' => array(),
        );
        $networks = NetworkManager::getAllNetworks();
        foreach ($networks as $network) {
            try {
                $data['updated'][$network->getNetworkName()] = $network->updateAuthorizationsPortion();
            } catch (NetworkException $e) {
                $data['updated'][$network->getNetworkName()] = $e->getMessage();
            }
        }
        $this->render('updateauthorizations', $data);
    }

    public function actionCheckAppInstalled() {
        // This feature request will not be implemented in this sprint
        // Ticket #52
    }

    protected function _assertAccessKey() {
        $key = YII::app()->request->getQuery('cronKey');
        if (0 !== strcasecmp($key, Yii::app()->params['cronKey'])) {
            throw new SecurityException('Wrong access key');
        }
    }

}

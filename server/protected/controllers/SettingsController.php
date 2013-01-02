<?php

class SettingsController extends BaseController {

    public function actionDisablenetwork() {
        $networkId = Yii::app()->request->getQuery('networkId');
        $network = $this->_getNetworkById($networkId);
        $this->_session->user->disableNetwork($network);
        $this->_rebuildSession();
    }

    public function actionEnablenetwork() {
        $networkId = Yii::app()->request->getQuery('networkId');
        $network = $this->_getNetworkById($networkId);
        $this->_session->user->enableNetwork($network);
        $this->_rebuildSession();
    }

    public function actionSetSettings() {
        $settings = Yii::app()->request->getPost('settings');
        $networksInfo = $this->_session->networksInfo;
        foreach ($settings as $setting) {
            if (isset($setting['id']) && isset($setting['enabled'])) {
                $network = $this->_getNetworkById($setting['id']);
                if (isset($setting['language']) && isset($networksInfo[$setting['id']])) {
                    $network->setAuthorizationLanguage(
                        $networksInfo[$setting['id']]['authorization_id'],
                        $setting['language']
                     );
                }
                if ($setting['enabled'] == 'true') {
                    $this->_session->user->enableNetwork($network);
                } else {
                    $this->_session->user->disableNetwork($network);
                }
            } else {
                throw new SecurityException('Some data lost');
            }
        }
        $this->_rebuildSession();
    }

}

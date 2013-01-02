<?php

class GPlusNetwork extends NetworkBase {

    public function getAuthorizationURL() {
        //
    }

    public function getNetworkName() {
        return $this->_t('NETWORKS::GPLUS_LOGIN');
    }

    public function getAuthorizationInfoByNetworkUserId($userId) {
        //
    }

    public function getAuthorizationInfoByUserId($userId) {
        //
    }

    public function checkAuthorizationExists() {
        //
    }

    public function createAuthorization($userId) {
        //
    }

    public function updateAuthorization() {
        //
    }

    public function updateAuthorizationsPortion() {
        //
    }

    public function setDataFromSession(array $data) {
        //
    }

    public function checkPostIdValid($postId) {
        return true;
    }

    protected function _getExternalIdFieldName() {
        return 'gp_user_id';
    }

    protected function _getUserData() {
        //
    }

}
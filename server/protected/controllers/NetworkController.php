<?php

class NetworkController extends BaseController {

    protected $_networkId;

    /**
     * @var NetworkBase
     */
    protected $_network;

    /**
     * Every call should contain networkId, so we can always have an instance of correct network
     */
    protected function beforeAction($action) {
        parent::beforeAction($action);
        $this->_networkId = YII::app()->request->getPost('networkId');
        $this->_network = $this->_initNetwork($this->_networkId);
		return true;
	}

    /**
     * This controller returns only JSON-encoded data
     */
    protected function  afterAction($action) {
        parent::afterAction($action);
        $this->renderAjax();
    }

    public function actionGetDislikes() {
        $ids = YII::app()->request->getPost('postIds');
        $this->viewData->dislikes = $this->_network->getDislikes(
            explode(',', $ids),
            $this->_network->getDislikesLimit()
        );
    }

    public function actionGetDislikesByPost() {
        $postId = YII::app()->request->getPost('postId');
        $page = (int)YII::app()->request->getPost('page');

        $this->_network->setNameFormat(NetworkBase::NAME_FORMAT_ONLY_NAME);
        
        $dislikes = $this->_network->getDislikes(
            array($postId),
            $this->_network->getExtendedDislikesLimit(),
            $this->_network->getExtendedDislikesLimit() * $page
        );

        $this->viewData->dislikes = empty($dislikes) ? null : $dislikes[$postId];
    }

    public function actionDislike() {
        $cancel = YII::app()->request->getPost('cancel');
        $postId = YII::app()->request->getPost('postId');
        
        $this->_network->dislike($postId, $cancel);

        $dislikes = $this->_network->getDislikes(
            array($postId),
            $this->_network->getDislikesLimit()
        );
        $this->viewData->people = empty($dislikes) ? array() : $dislikes[$postId];
    }

}

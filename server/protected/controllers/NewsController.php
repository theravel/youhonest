<?php

class NewsController extends BaseController {

    public function actionNews() {
        $this->viewData->news = null;

        if ($this->_user !== null) {
            $language = Yii::app()->request->getPost('settings');
            $userDate = $this->_user->getLatestShownNewsDate();
            $news = NewsManager::getInstance()->getNews();
            $newsCount = count($news);
            for ($i = $newsCount - 1; $i >= 0; $i--) {
                // @TODO language
                // $news[$i]->language
                if ($userDate < $news[$i]->created_date) {
                    $this->_session->user->setLatestShownNewsDate($news[$i]->created_date);
                    $this->_rebuildSession();

                    $this->viewData->news = $news[$i]->getAttributes();
                }
            }
        }
        $this->renderAjax();
    }

}
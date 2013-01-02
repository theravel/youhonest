<?php

abstract class NetworkBase {

    const CONTENT_JSON = 'JSON';
    const CONTENT_HTML = 'HTML';

    const NAME_FORMAT_ONLY_NAME = 'ONLY_NAME';
    const NAME_FORMAT_BOTH = 'BOTH';

    protected $_redirectURL;
    protected $_authorizationTable;
    protected $_commentsTable;
    protected $_dislikesTable;

    protected $_dislikesLimit;
    protected $_extendedDislikesLimit;
    protected $_nameFormat = self::NAME_FORMAT_BOTH;

    protected $_networkId;
    protected $_authID;
    
    /**
     * @var NetworkModel
     */
    protected $_originalModel;

    public function __construct(NetworkModel $originalModel) {
        $this->_originalModel = $originalModel;
        $this->_redirectURL = Yii::app()->request->hostInfo .
                              YII::app()->baseUrl .
                              '/index/return/network_id/' . $originalModel->network_id;
    }

    public function getNetworkId() {
        return $this->_originalModel->network_id;
    }

    public function getUrlPattern() {
        return $this->_originalModel->url_pattern;
    }

    public function getUrl() {
        return $this->_originalModel->url;
    }

    public function getIcon() {
        return $this->_originalModel->icon;
    }

    public function isEnabled() {
        return $this->_originalModel->enabled;
    }

    public function getUserEnabledField() {
        return $this->_originalModel->user_enabled_field;
    }

    public function getDislikesLimit() {
        return $this->_dislikesLimit;
    }

    public function getExtendedDislikesLimit() {
        return $this->_extendedDislikesLimit;
    }

    public function setNameFormat($format) {
        $this->_nameFormat = $format;
    }

    public function setAuthorizationLanguage($authId, $language) {
        $model = $this->_getAuthorizationModel(false);
        $userRow = $model->findByPk($authId);
        if (null === $userRow) {
            throw new NetworkException('Such auth does not exist');
        }
        $userRow->locale = $language;
        $userRow->save();
    }

    /**
     * Dislike post or cancel post dislike
     * @param int $postId
     * @param bool $cancelDislike
     * @throws NetworkException
     * @return array of DislikeEntity
     */
    public function dislike($postId, $cancelDislike = false) {
        if (!$this->checkPostIdValid($postId)) {
            throw new NetworkException("Post id '$postId' is not valid");
        }

        $model = $this->_getDislikeModel(!$cancelDislike);
        if ($cancelDislike) {
            // we do not check if dislike exists or not
            // it does not matter and this improves performance
            $model->deleteDislike($this->_authID, $postId);
        } else {
            $model->authorization_id = $this->_authID;
            $model->post_id = $postId;
            $model->date = TimeUtilities::getInstance()->now();
            try {
                // here we check if such dislike already exists
                // DB has unique index and will throw exceptions if row exists
                $model->save();
            } catch (CDbException $e) {
                throw new NetworkException('Such dislike already exists', 500, $e);
            }
        }

        // Update dislikes count
        // !!! here is used not normal table structure for better performance
        $model->updateDislikesCount($postId, $cancelDislike ? -1 : +1);
    }

    /**
     * Find all dislikes for needed posts
     * @param array $postIds
     * @param int $limit
     * @param int $offset
     * @return array(
     *     'postId' => array(
     *        'dislikes' => array of DislikeEntity,
     *        'hasMy' => bool,
     *     ),
     * )
     */
    public function getDislikes(array $postIds, $limit, $offset = null) {
        $model = $this->_getDislikeModelWithAuthorizationInfo();
        $criteria = new CDbCriteria();
        $criteria->addInCondition('post_id', $postIds);
        if ($offset === null) {
            // my dislike should be first (or it can break 'hasMy' functionality
            $criteria->addCondition(
                DislikeModel::getSelectionLimitCondition(0, $limit, $this->_authID)
            );
            // we need this, because sometimes $limit == 1, so user wants to get its own dislike
            $order = DislikeModel::getOrderByMeFirst($this->_authID);
        } else {
            // we need limited count starting from $offset
            // there's no need to set user's dislike first
            $criteria->addCondition(DislikeModel::getSelectionLimitCondition($offset, $limit));
            $order = 'dislike_id';
        }
        $criteria->order = $order;
        $dislikes = $model->findAll($criteria);

        $result = array();
        if (!empty($dislikes)) {
            $idField = $this->_getExternalIdFieldName();
            foreach ($dislikes as $dislike) {
                $object = new DislikeEntity;
                $object->id = $dislike->authorizationInfo->$idField;
                $object->name = $this->_getUserName($dislike->authorizationInfo);
                $object->photo = $dislike->authorizationInfo->photo_url;

                if (isset($result[$dislike->post_id])) {
                    $result[$dislike->post_id]['dislikes'][] = $object;
                } else {
                    $result[$dislike->post_id] = array(
                        'dislikes' => array($object),
                        'hasMy' => false,
                        'count' => $dislike->dislikes_count,
                    );
                }

                if ($dislike->authorization_id == $this->_authID) {
                    $result[$dislike->post_id]['hasMy'] = true;
                }
            }
        }
        return $result;
    }

    /**
     * @return bool
     */
    abstract public function checkAuthorizationExists();

    /**
     * @return int athorization_id
     */
    abstract public function createAuthorization($userId);

    /**
     * Updates expired authorizations
     * @return int authorization_id
     */
    abstract public function updateAuthorization();

    /**
     * @return string
     */
    abstract public function getAuthorizationURL();

    /**
     * @return string
     */
    abstract public function getNetworkName();

    /**
     * Get info by internal userId
     * @return array
     */
    abstract public function getAuthorizationInfoByUserId($userId);

    /**
     * Get info by external userId
     * @return array
     */
    abstract public function getAuthorizationInfoByNetworkUserId($userId);

    /**
     * Initializes internal fields by session data
     */
    abstract public function setDataFromSession(array $data);

    /**
     * Check if such postId is valid
     * @return bool
     */
    abstract public function checkPostIdValid($postId);

    /**
     * Update names, photos and other data of users
     */
    abstract public function updateAuthorizationsPortion();

    /**
     * Get BD field name that contains external id
     */
    abstract protected function _getExternalIdFieldName();

    /**
     * This method can be overriden if some networks requires another logic
     * @param object $authorizationInfo
     * @return string
     */
    protected function _getUserName($authorizationInfo) {
        return $authorizationInfo->first_name . ' ' . $authorizationInfo->last_name;
    }

    /**
     * @param bool $new return insert scenario or update, insert is default
     * @return AuthorizationModel
     */
    protected function _getAuthorizationModel($new = true) {
        AuthorizationModel::setTableName($this->_originalModel->authorization_table);
        if ($new) {
            return new AuthorizationModel();
        } else {
            $model = AuthorizationModel::model();
            $model->refreshMetaData();
            return $model;
        }
    }

    /**
     * @param bool $new return insert scenario or update, insert is default
     * @return DislikeModel
     */
    protected function _getDislikeModel($new = true) {
        DislikeModel::setTableName($this->_originalModel->dislike_table);
        if ($new) {
            return new DislikeModel();
        } else {
            $model = DislikeModel::model();
            $model->refreshMetaData();
            return $model;
        }
    }

    protected function _getDislikeModelWithAuthorizationInfo() {
        AuthorizationModel::setTableName($this->_originalModel->authorization_table);
        AuthorizationModel::model()->refreshMetaData();
        return $this->_getDislikeModel(false)->with('authorizationInfo');
    }

    protected function _t($key) {
        return Translation::getInstance()->translate($key);
    }
    
}
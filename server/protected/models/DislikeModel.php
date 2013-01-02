<?php

class DislikeModel extends CActiveRecord {

    // it is impossible or too difficult to build such query using ActiveRecord
    const UPDATE_DISLIKES_COUNT = '
        SET @row = 0;
        UPDATE :tableName
        SET
            dislikes_count = (
                SELECT dislikes_count FROM (
                    SELECT dislikes_count
                    FROM :tableName
                    WHERE post_id = :postId
                    ORDER BY dislike_id
                    LIMIT 1
                ) AS temp_table
            ) + :delta,
            grouping_number = (@row := @row + 1)
        WHERE post_id = :postId';

    // this is written with plain SQL because of performance reasons
    const DELETE_DISLIKE = '
        DELETE FROM :tableName
        WHERE authorization_id = :authId AND post_id = :postId
        LIMIT 1';

    const SPECIAL_SELECTION_ORDER = 'CASE WHEN t.authorization_id = %d THEN 1 ELSE dislike_id END';

    const SELECTION_LIMIT_CONDITION_WITH_AUTH_ID = '
        (t.grouping_number > %d AND t.grouping_number <= %d)
        OR t.authorization_id = %d';

    const SELECTION_LIMIT_CONDITION = '(t.grouping_number > %d AND t.grouping_number <= %d)';

    protected static $_tableName;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public static function setTableName($tableName) {
        self::$_tableName = $tableName;
    }

    public function tableName() {
        return self::$_tableName;
    }

    public function primaryKey() {
        return 'authorization_id';
    }

    public function relations() {
        return array(
            'authorizationInfo' => array(self::BELONGS_TO, 'AuthorizationModel', 'authorization_id'),
        );
    }

    public static function getOrderByMeFirst($authId) {
        return sprintf(self::SPECIAL_SELECTION_ORDER, $authId);
    }

    public static function getSelectionLimitCondition($offset, $limit, $authId = null) {
        if ($authId) {
            return sprintf(self::SELECTION_LIMIT_CONDITION_WITH_AUTH_ID, $offset, $offset + $limit, $authId);
        } else {
            return sprintf(self::SELECTION_LIMIT_CONDITION, $offset, $offset + $limit);
        }
    }

    public function deleteDislike($authId, $postId) {
        $query = $this->_getQuery(self::DELETE_DISLIKE);
        Yii::app()->db->createCommand($query)
            ->bindValue(':authId', $authId)
            ->bindValue(':postId', $postId)
            ->query();
    }

    /**
     * @param int $postId
     * @param int $delta [+1 or -1]
     */
    public function updateDislikesCount($postId, $delta) {
        $query = $this->_getQuery(self::UPDATE_DISLIKES_COUNT);
        $query = str_replace(':delta', $delta, $query);
        Yii::app()->db->createCommand($query)
            ->bindValue(':postId', $postId)
            ->query();
    }

    public function _getQuery($query) {
        return preg_replace('/\:tableName/', self::$_tableName, $query);
    }
}
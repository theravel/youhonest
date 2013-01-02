<?php

class AuthorizationModel extends CActiveRecord {

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
            'dislikes' => array(self::HAS_ONE, 'DislikeModel', 'authorization_id'),
        );
    }
}
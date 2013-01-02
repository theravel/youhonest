<?php

class TranslationModel extends CActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'translations';
    }

    public function primaryKey() {
        return 'translation_id';
    }
}

<?php

namespace app\models;

use Yii;

class Lang extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'preferred_lang';
    }

    public function attributeLabels() {
        return [
            'code' => 'Код',
            'active' => 'Активен',
            'title' => 'Название'
        ];
    }

    public static function getActiveLangs() {
        $langs = self::find()
            ->select(['code', 'title'])
            ->where(['active' => 1])
            ->all()
        ;

        $arr = [];
        foreach ($langs as $lang) {
            $arr[$lang->code] = $lang->title;
        }

        return $arr;
    }
}
<?php

namespace app\models;

use Yii;

class Category extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'category';
    }

    public function rules() {
        return [
            [['title'], 'filter', 'filter' => 'trim'],
            [['title', 'visible'], 'required'],
            ['title', 'string', 'min' => 3, 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'visible' => 'Видимость'
        ];
    }

    public static function getCategories() {
        $categories = self::find()
            ->select(['id', 'title'])
            ->all()
        ;

        $arr = [];
        foreach ($categories as $category) {
            $arr[$category->id] = $category->title;
        }

        return $arr;
    }
}
<?php

namespace app\models;

use yii\base\Model;
use app\models\News;
use Yii;

class NewsSearch extends Model {

    public $id;
    public $capt;
    public $id_category;
    public $login;

    private $params;

    public function __construct($params = []) {
        $this->params = Yii::$app->request->queryParams;
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'capt' => 'Заголовок',
            'id_category' => 'Категория',
            'login' => 'Логин'
        ];
    }

    public function rules() {
        return [];
    }

    public function search() {

        $where['news.active'] = 1;

        if ($params = $this->params) {

            if (isset($params['id']) && $params['id']) {
                $id = (int) $params['id'];

                $where['news.id'] = $id;
                $this->id = $id;
            }

            if (isset($params['capt']) && $params['capt']) {
                $capt = ['like', 'news.capt', $params['capt']];
                $this->capt = $params['capt'];
            }

            if (isset($params['login']) && $params['login'] && (Yii::$app->user->identity->role != User::ROLE_USER) ) {
                $where['user.login'] = $params['login'];
                $this->login = $params['login'];
            }

            if (isset($params['id']) && 0 != (int) $params['id_category']) {
                $id_category = (int) $params['id_category'];

                $where['news.id_category'] = $id_category;
                $this->id_category = $id_category;
            }
        }

        if (Yii::$app->user->identity->role == User::ROLE_USER) {
            $where['news.userId'] = Yii::$app->user->id;
        }

        $query = News::find()
            ->innerJoinWith(['user'])
            ->where($where);

        if (isset($capt) && $capt) {

            $query->andWhere($capt);
        }

        return $query->orderBy(['news.dt' => SORT_DESC]);
    }

}
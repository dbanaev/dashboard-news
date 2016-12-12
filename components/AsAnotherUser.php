<?php
namespace app\components;

use app\models\User;
use app\models\User as UserModel;
use yii\web\NotFoundHttpException;
use Yii;

class AsAnotherUser {

    private $_targetUser;

    public function login($id) {

        $this->_targetUser = User::findOne(['id' => (int) $id]);

        if ( !$this->_targetUser ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        Yii::$app->session->set('anotherUserId', Yii::$app->user->id);
        Yii::$app->user->login($this->_targetUser, 3600*24*30);

        return Yii::$app->getResponse()->redirect(['news/index']);
    }

    public function logOut() {
        if ( $anotherUserId = Yii::$app->session->get('anotherUserId') ) {

            Yii::$app->session->remove('anotherUserId');
            Yii::$app->user->login(UserModel::findOne(['id' => (int) $anotherUserId]), 3600*24*30);
        }

        return Yii::$app->getResponse()->redirect(['news/index']);
    }

    public function check() {
        return Yii::$app->session->has('anotherUserId');
    }
}
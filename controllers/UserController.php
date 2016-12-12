<?php

namespace app\controllers;

use app\models\AddUserForm;
use app\models\UpdateUserForm;
use app\models\User;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use Yii;

class UserController extends BehaviorsController {

    public function init() {
        parent::init();

        if ( Yii::$app->user->identity->role != User::ROLE_ROOT ) {
            throw new NotFoundHttpException('Страница не найдена!');
        }
    }

    protected function checkAccess() {
        if ( Yii::$app->user->identity->role != User::ROLE_ROOT ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionIndex() {

        $this->checkAccess();

        //$users = User::find()->where(['<>', 'id', Yii::$app->user->id])->all();
        $users = User::find()->all();

        return $this->render('index', [
            'users' => $users
        ]);
    }

    public function actionAdd() {

        $this->checkAccess();

        $model = new AddUserForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = $model->add()) {

                Yii::$app->session->setFlash('success', 'Пользователь <strong>' . Html::encode($user->login) . '</strong> успешно создан');

            } else {

                Yii::$app->session->setFlash('error', 'Возникла ошибка при создании пользователя');
                Yii::error('Ошибка создания пользователя');
            }

            return $this->refresh();
        }

        return $this->render('add', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id = null) {

        $this->checkAccess();

        $user = User::find()
            ->where(['id' => (int) $id])
            ->andWhere(['<>', 'login', 'root'])
            ->one()
        ;

        if (!$user) {
            return $this->redirect(['user/index']);
        }

        $model = new UpdateUserForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->update()) {
                Yii::$app->session->setFlash('success', 'Пользователь изменен');
            } else {
                Yii::$app->session->setFlash('error', 'Пользователь не изменен');
                Yii::error('Ошибка записи. Пользователь не изменен');

                return $this->refresh();
            }
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionDelete($id = null) {

        $this->checkAccess();

        $id = (int) $id;

        if ($id) {

            $result = Yii::$app->db
                ->createCommand()
                ->delete(User::tableName(), ['id' => $id])
                ->execute()
            ;

            if ($result) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно удален');
            } else {
                Yii::$app->session->setFlash('error', 'Пользователь не удален');
                Yii::error('Ошибка. Пользователь не удален');
            }
        }

        return $this->redirect(['user/index']);
    }

    public function actionEnter($id) {
        return Yii::$app->asAnotherUser->login($id);
    }
}
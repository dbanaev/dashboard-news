<?php

namespace app\controllers;

use app\models\Category;
use app\models\User;
use yii\web\NotFoundHttpException;
use Yii;

class CategoryController extends BehaviorsController {

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

        $categories = Category::find()->all();

        return $this->render('index', ['categories' => $categories]);
    }

    public function actionAdd() {

        $this->checkAccess();

        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {

                Yii::$app->session->setFlash('success', 'Категория успешно создана');

            } else {

                Yii::$app->session->setFlash('error', 'Возникла ошибка при создании категории');
                Yii::error('Ошибка создания категории');
            }

            return $this->refresh();
        }

        return $this->render('add', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id = null) {

        $this->checkAccess();

        $model = Category::find()
            ->where(['id' => (int) $id])
            ->andWhere(['<>', 'id', 5])
            ->one()
        ;

        if (!$model) {
            return $this->redirect(['category/index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {

                Yii::$app->session->setFlash('success', 'Категория успешно изменена');

            } else {

                Yii::$app->session->setFlash('error', 'Возникла ошибка при изменении категории');
                Yii::error('Ошибка изменении категории');
            }

            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionDelete($id = null) {

        $this->checkAccess();

        $result = Yii::$app->db
            ->createCommand()
            ->delete(Category::tableName(), ['id' => (int) $id])
            ->execute()
        ;

        if ($result) {
            Yii::$app->session->setFlash('success', 'Категория успешно удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Категория не удалена');
            Yii::error('Ошибка. Категория не удалена');
        }

        return $this->redirect(['category/index']);
    }
}
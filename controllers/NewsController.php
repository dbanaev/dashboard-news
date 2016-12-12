<?php

namespace app\controllers;

use app\helpers\UploadedImage;
use app\models\News;
use app\models\NewsSearch;
use app\models\User;
use yii\data\Pagination;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use Yii;

class NewsController extends BehaviorsController {

    public function actionIndex() {

        $model = new NewsSearch(Yii::$app->request->queryParams);

        $query = $model->search();

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $news = $query->offset($pages->offset)
            //->limit($pages->limit)
            ->limit(50)
            ->all()
            ;

        return $this->render('index', [
            'model' => $model,
            'news' => $news,
            'pages' => $pages
        ]);
    }

    public function actionPreview() {

        $newsId = (int) Yii::$app->request->post('id');

        $news = News::find()
            ->where(['id' => (int) $newsId])
            ->one()
        ;

        if (!$news) die('');

        if ($news->userId != Yii::$app->user->id) {

            if ( !in_array(Yii::$app->user->identity->role, [User::ROLE_ROOT, User::ROLE_ADMIN, User::ROLE_MODER]) ) {
                die('');
            }
        }

        die($this->renderPartial('_preview', [
            'item' => $news,
            'isMediadog' => (Yii::$app->user->identity->login == 'mediadog')
        ]));
    }

    public function actionAdd() {

        $model = new News();
        $model->scenario = News::SCENARIO_CREATE;
        $model->lang_code = 'ru';

        if ($model->load(Yii::$app->request->post())) {
            $model->uploaded = UploadedImage::getInstance($model, 'uploaded');

            $model->userId = $model->lastUserId = Yii::$app->user->id;
            $model->dt = $model->dt_last = time();

            $title = $model->capt;
            $title = str_replace(['%', '_'], '', $title);

            $url_short = News::generateUrl($title);

            if (News::checkDuplicates($url_short)) {
                Yii::$app->session->setFlash('error', 'Новость дублируется');
                Yii::error('Ошибка создания новости. Дублирование.');

                return $this->redirect(['news/index']);
            }

            $model->url_short = $url_short;
            $model->url_full = News::generateUrl($title, '-full');

            if ($model->save()) {

                $id = $model->id;

                $model->url_short = $id . '-' . $model->url_short;
                $model->url_full = $id . '-' . $model->url_full;

                $model->resizeImage();

                try {
                    $result = $model->update();
                } catch (Exception $e) {
                    $model->delete();

                    Yii::$app->session->setFlash('error', 'Новость удалена. Ошибка генерации url');
                    Yii::error('Ошибка генерации url');

                    return $this->redirect(['news/index']);
                }

                if ($result === false) {
                    $model->delete();
                    Yii::$app->session->setFlash('error', 'Новость удалена. Ошибка генерации url');
                    Yii::error('Ошибка генерации url');
                    return $this->redirect(['news/index']);
                }

                return $this->redirect(['news/update', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при создании новости');
                Yii::error('Ошибка создания новости');
            }
        }

        return $this->render('add', [
            'model' => $model
        ]);
    }

    public function actionMulti() {

        $news = [];
        for ($i = 1; $i <= 5; $i++) {
            $news[$i] = new News();
            $news[$i]->scenario = News::SCENARIO_CREATE;
            $news[$i]->lang_code = 'ru';
        }

        if ( News::loadMultiple($news, Yii::$app->request->post()) ) {

            $addedCount = 0;
            for ($i = 1; $i <= count($news); $i++) {

                if (
                    empty($news[$i]->capt) &&
                    empty($news[$i]->txt_short) &&
                    empty($news[$i]->txt_full) &&
                    empty($news[$i]->id_category) &&
                    empty($news[$i]->source_name) &&
                    empty($news[$i]->source_link)
                ) continue;

                $news[$i]->uploaded = UploadedImage::getInstance($news[$i], '[' . $i . ']uploaded');

                if ($news[$i]->validate()) {

                    $news[$i]->userId = $news[$i]->lastUserId = Yii::$app->user->id;
                    $news[$i]->dt = $news[$i]->dt_last = time();

                    $title = $news[$i]->capt;
                    $title = str_replace(['%', '_'], '', $title);

                    $url_short = News::generateUrl($title);

                    if (News::checkDuplicates($url_short)) {
                        Yii::error('Ошибка создания новости. Дублирование.');
                        continue;
                    }

                    $news[$i]->url_short = $url_short;
                    $news[$i]->url_full = News::generateUrl($title, '-full');

                    if ($news[$i]->save()) {
                        $id = $news[$i]->id;

                        $news[$i]->url_short = $id . '-' . $news[$i]->url_short;
                        $news[$i]->url_full = $id . '-' . $news[$i]->url_full;

                        $news[$i]->resizeImage();

                        try {
                            $result = $news[$i]->update();
                        } catch (Exception $e) {
                            $news[$i]->delete();
                            Yii::error('Ошибка генерации url');

                            continue;
                        }

                        if ($result === false) {
                            $news[$i]->delete();
                            Yii::error('Ошибка генерации url');

                            continue;
                        }

                        $addedCount++;
                    }
                }
            }

            if ($addedCount > 0) {
                Yii::$app->session->setFlash('success', $addedCount . ' новостей успешно добавлено');
                return $this->redirect(['news/index']);
            }
            
            Yii::$app->session->setFlash('error', 'Нет добавленных новостей');
            Yii::error('Ошибка создания несколько новостей');
        }

        return $this->render('multi', [
            'news' => $news
        ]);
    }

    public function actionUpdate($id = null) {

        $model = News::find()
            ->where(['id' => (int) $id])
            ->one()
        ;

        if (!$model) {
            return $this->redirect(['news/index']);
        }

        if ($model->userId != Yii::$app->user->id) {

            if ( !in_array(Yii::$app->user->identity->role, [User::ROLE_ROOT, User::ROLE_ADMIN, User::ROLE_MODER]) ) {
                throw new NotFoundHttpException('Страница не найдена!');
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->uploaded = UploadedImage::getInstance($model, 'uploaded');

            $model->lastUserId = Yii::$app->user->id;
            $model->dt_last = time();

            if ($model->save()) {

                if ($model->uploaded) {

                    $model->resizeImage();

                    try {
                        $model->update();
                    } catch (Exception $e) {
                        $model->delete();

                        Yii::$app->session->setFlash('error', 'Новость удалена. Ошибка генерации url');
                        Yii::error('Ошибка генерации url');

                        return $this->redirect(['news/index']);
                    }
                }

                Yii::$app->session->setFlash('success', 'Новость успешно изменена');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при изменении новости');
                Yii::error('Ошибка изменении новости');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'isMediadog' => (Yii::$app->user->identity->login == 'mediadog')
        ]);
    }

    public function actionDelete($id = null) {
        exit(0);
        $model = News::find()
            ->where(['id' => (int) $id])
            ->one()
        ;

        if (!$model) {
            return $this->redirect(['news/index']);
        }

        if ($model->userId != Yii::$app->user->id) {

            if ( !in_array(Yii::$app->user->identity->role, [User::ROLE_ROOT, User::ROLE_ADMIN]) ) {
                throw new NotFoundHttpException('Страница не найдена!');
            }
        }

        $filename = $model->img_file;

        $filename = explode('.', $filename);

        $ext = $filename[1];
        $filename = $filename[0];

        $path = '/admin/dimg/';

        if ($model->delete()) {

            if (file_exists($path . $model->img_file)) {
                unlink($path . $model->img_file);
            }

            if (file_exists($path . $filename . '_800.' . $ext)) {
                unlink($path . $filename . '_800.' . $ext);
            }

            if (file_exists($path . $filename . '_16x9.' . $ext)) {
                unlink($path . $filename . '_16x9.' . $ext);
            }

            Yii::$app->session->setFlash('success', 'Новость успешно удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Новость не удалена');
            Yii::error('Ошибка. Новость не удалена');
        }

        return $this->redirect(['news/index']);
    }
}
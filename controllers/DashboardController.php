<?php

namespace app\controllers;

use app\models\LoginForm;
use Yii;

class DashboardController extends BehaviorsController {

    public function actionCaptcha() {

        if (Yii::$app->session->has('captcha')) {
            $text = Yii::$app->session->get('captcha');
            $nChars = strlen($text);

            $path = '/var/www/oreshki/data/www/showcase-admin-area/web/images/bg.png';

            $img = imagecreatefrompng($path);

            $color = imagecolorallocate($img, 64, 64, 64);
            $x = 20; $y = 30; $deltaX = 40;

            for ($i = 0; $i < $nChars; $i++) {
                $size = rand(18, 30);
                $angle = -30 + rand(0, 60);

                imagettftext($img, $size, $angle, $x, $y, $color,  '/var/www/oreshki/data/www/showcase-admin-area/web/fonts/bellb.ttf', $text[$i]);

                $x += $deltaX;
            }

            header('Content-Type: image/png');
            imagepng($img, null, 6);
        }
    }

    public function actionLogin() {

        if (!Yii::$app->user->isGuest) {

            return $this->redirect(['news/index']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->redirect(['news/index']);
        }

        return $this->render('login', [
            'model' => $model
        ]);
    }

    public function actionLogout() {

        Yii::$app->session->remove('captcha');
        Yii::$app->user->logout();

        return $this->redirect(['login']);
    }

    public function actionReturnRoot() {
        return Yii::$app->asAnotherUser->logOut();
    }
}
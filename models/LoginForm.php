<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model {

    public $login;
    public $password;
    public $rememberMe = true;
    public $captcha;

    private $_user = false;

    public function rules() {
        return [
            [['login', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword']
        ];
    }

    public function attributeLabels() {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
            'captcha' => 'Код с картинки'
        ];
    }

    public static function generateCaptcha() {
        $code = substr(md5(uniqid()), 0, 5);
        Yii::$app->session->set('captcha', $code);
    }

    public function validatePassword($attribute) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user) {
                $this->addError($attribute, 'Неверное имя пользователя');
                return false;
            }

            if (!$user->validatePassword($this->password)) {

                $this->addError($attribute, 'Неверный пароль');

                if ($user->fail_password_cnt != 3) {
                    $user->fail_password_cnt++;
                    $user->save();

                } else {

                    self::generateCaptcha();
                }

                return false;
            }
        }
    }

    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByLogin($this->login);
        }

        return $this->_user;
    }

    public function login() {
        if ($this->validate()) {
            $user = $this->getUser();

            if ($user) {

                if ($user->fail_password_cnt != 3) {

                    Yii::$app->session->remove('captcha');

                    $user->fail_password_cnt = 0;
                    $user->save();

                    return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
                }

                if (Yii::$app->session->has('captcha')) {
                    $loginForm = Yii::$app->request->post('LoginForm');

                    if (!isset($loginForm['captcha']) || !$loginForm['captcha']) {
                        self::generateCaptcha();

                        $this->addError('captcha', 'Вы не ввели код с картинки');
                        return false;
                    }

                    if (Yii::$app->session->get('captcha') != $loginForm['captcha']) {
                        self::generateCaptcha();

                        $this->addError('captcha', 'Код с картинки введен неверно');
                        return false;
                    }

                    Yii::$app->session->remove('captcha');

                    $user->fail_password_cnt = 0;
                    $user->save();

                    return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
                }

                self::generateCaptcha();
            }
        }

        return false;
    }
}
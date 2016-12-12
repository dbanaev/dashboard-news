<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class User extends \yii\db\ActiveRecord implements IdentityInterface {

    const ROLE_ROOT = 0;
    const ROLE_ADMIN = 1;
    const ROLE_MODER = 2;
    const ROLE_USER = 3;

    public $password;

    public static function tableName() {
        return 'user';
    }

    public function rules() {
        return [
            [['login', 'password'], 'filter', 'filter' => 'trim'],
            [['login', 'role'], 'required'],
            ['login', 'string', 'min' => 4, 'max' => 50],
            ['login', 'unique', 'message' => 'Данный логин уже используется']
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'password' => 'Пароль',
            'role' => 'Роль',
            'auth_key' => 'Auth Key',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    public static function getRoles() {
        return [
            self::ROLE_ROOT => 'root',
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_MODER => 'Модератор',
            self::ROLE_USER => 'Пользователь'
        ];
    }

    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findByLogin($login) {
        return self::findOne([
            'login' => $login
        ]);
    }


    public static function findIdentity($id) {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }
}
<?php

namespace app\models;

use yii\base\Model;
use Yii;
use yii\db\Expression;

class AddUserForm extends Model {

    public $login;
    public $password;
    public $role;

    public function rules() {
        return [
            [['login', 'password'], 'filter', 'filter' => 'trim'],
            [['login', 'password', 'role'], 'required'],
            ['password', 'string', 'min' => 6, 'max' => 50],
            ['login', 'unique',
                'targetClass' => User::className(),
                'message' => 'Данный логин уже используется'
            ],
            ['password', 'checkPassword']
        ];
    }

    public function checkPassword() {
        if (!$this->hasErrors()) {
            $password = $this->password;

            if (!preg_replace('/\D/si', '', $password)) {
                $this->addError('password', 'В пароле должна присутствовать хотя бы одна цифра');
                return false;
            }

            if (!preg_replace('/\d/si', '', $password)) {
                $this->addError('password', 'В пароле должна присутствовать хотя бы одна буква');
                return false;
            }
        }
    }

    public function attributeLabels() {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
            'role' => 'Роль'
        ];
    }

    public function add() {
        $user = new User();
        $user->login = $this->login;
        $user->role = $this->role;

        $user->created_at = $user->updated_at = new Expression('NOW()');

        $user->setPassword($this->password);

        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
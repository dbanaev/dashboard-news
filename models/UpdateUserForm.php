<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Expression;

class UpdateUserForm extends Model {

    public $login;
    public $newPassword;
    public $role;

    protected $_user;

    public function __construct(User $user) {
        $this->_user = $user;

        $this->login = $user->login;
        $this->role = $user->role;
    }

    public function rules() {
        return [
            [['role'], 'required'],
            ['newPassword', 'string', 'min' => 6, 'max' => 50],
            ['newPassword', 'checkPassword']
        ];
    }

    public function attributeLabels() {
        return [
            'login' => 'Логин',
            'newPassword' => 'Новый пароль',
            'role' => 'Роль'
        ];
    }

    public function checkPassword() {
        if (!$this->hasErrors()) {
            $newPassword = $this->newPassword;

            if (!preg_replace('/\D/si', '', $newPassword)) {
                $this->addError('newPassword', 'В пароле должна присутствовать хотя бы одна цифра');
                return false;
            }

            if (!preg_replace('/\d/si', '', $newPassword)) {
                $this->addError('newPassword', 'В пароле должна присутствовать хотя бы одна буква');
                return false;
            }
        }
    }

    public function update() {
        $user = $this->_user;

        $user->role = $this->role;
        $user->updated_at = new Expression('NOW()');

        if ($this->newPassword) {
            $user->setPassword($this->newPassword);
        }

        return $user->save() ? true : false;
    }
}
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

$this->title = 'Редактировать';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['user/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="option-panel">
        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group field-updateuserform-login">
            <label class="control-label" for="updateuserform-login">Логин</label>
            <input type="text" id="updateuserform-login" class="form-control" disabled value="<?= $model->login ?>">
        </div>

        <?= $form->field($model, 'newPassword')->passwordInput(['placeholder' => 'Введите новый пароль']) ?>
        <?= $form->field($model, 'role')->dropDownList(User::getRoles()); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

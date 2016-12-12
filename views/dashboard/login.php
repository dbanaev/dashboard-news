<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="main-login">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login')->textInput() ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <?php if (Yii::$app->session->has('captcha')): ?>
        <?= $form->field($model, 'captcha')->textInput() ?>
        <?= Html::img('/admin/dashboard/captcha', ['class' => 'thumbnail']) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

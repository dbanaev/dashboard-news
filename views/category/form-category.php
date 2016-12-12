<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
?>

<div class="row">
    <div class="option-panel">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title')->textInput() ?>
        <?= $form->field($model, 'visible')->dropDownList([
            1 => 'Видимая',
            0 => 'Невидимая'
        ]); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
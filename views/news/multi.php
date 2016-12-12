<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use app\models\Lang;

$this->title = 'Добавить несколько';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['news/index']];
$this->params['breadcrumbs'][] = $this->title;

$categories = ['' => 'Не выбрано'];
$categories += Category::getCategories();

$langs = Lang::getActiveLangs();

?>

<div class="row">
    <div class="option-panel">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'enableClientValidation' => false
        ]); ?>

        <?php for ($i = 1; $i <= count($news); $i++): ?>
            <h1>Новость <?= $i ?></h1>

            <?= $form->field($news[$i], '[' . $i . ']capt')->textInput() ?>

            <?= $form->field($news[$i], '[' . $i . ']id_category')->dropDownList($categories) ?>
            <?= $form->field($news[$i], '[' . $i . ']lang_code')->dropDownList($langs, ['class' => 'form-control chosen-select']); ?>
            <?= $form->field($news[$i], '[' . $i . ']txt_full')->textarea(['class' => 'form-control ckeditor']) ?>
            <?= $form->field($news[$i], '[' . $i . ']txt_short')->textarea(['rows' => '7']) ?>

            <div class="form-group">
                <?= Html::button('Генерировать краткий', ['class' => 'btn btn-primary gen-short', 'data-fieldid' => $i]) ?>
            </div>

            <?= $form->field($news[$i], '[' . $i . ']source_name')->textInput() ?>
            <?= $form->field($news[$i], '[' . $i . ']source_link')->textInput() ?>

            <?= $form->field($news[$i], '[' . $i . ']uploaded')->fileInput() ?>

        <?php endfor; ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', '/news/index', ['class' => 'btn btn-info']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
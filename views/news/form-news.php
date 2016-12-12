<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use app\models\Category;
    use app\models\Lang;

    $categories = ['' => 'Не выбрано'];
    $categories += Category::getCategories();

    $langs = Lang::getActiveLangs();
?>

<div class="row">
    <div class="option-panel">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'capt')->textInput() ?>

        <?php if (!$model->isNewRecord): ?>

            <?php $hostname = Yii::$app->getUrlManager()->getHostInfo(); ?>

            <?php $urlShort = $hostname . '/' . $model->url_short; ?>
            <?php $urlFull = $hostname . '/' . $model->url_full; ?>

            <?php
                if (isset($isMediadog) && $isMediadog) {
                    $urlShort .= '?id=' . $model->id;
                    $urlFull .= '?id=' . $model->id;
                }
            ?>

            <div class="form-group required">
                <label class="control-label">Адрес краткой новости</label>
                <input type="text" class="form-control" value="<?= $urlShort ?>" disabled>
            </div>

            <div class="form-group required">
                <label class="control-label">Адрес полной новости</label>
                <input type="text" class="form-control" value="<?= $urlFull ?>" disabled>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'id_category')->dropDownList($categories) ?>
        <?= $form->field($model, 'lang_code')->dropDownList($langs, ['class' => 'form-control chosen-select']); ?>
        <?= $form->field($model, 'txt_full')->textarea(['class' => 'form-control ckeditor']) ?>
        <?= $form->field($model, 'txt_short')->textarea(['rows' => '7']) ?>

        <div class="form-group">
            <?= Html::button('Генерировать краткий', ['class' => 'btn btn-primary gen-short']) ?>
        </div>

        <?= $form->field($model, 'source_name')->textInput() ?>
        <?= $form->field($model, 'source_link')->textInput(['placeholder' => 'Адрес должен начинаться с http:// или https://']) ?>

        <?php if ($model->img_file): ?>
            <?= Html::img('/dimg/' . $model->img_file, ['class' => 'thumbnail']) ?>
        <?php endif; ?>

        <?= $form->field($model, 'uploaded')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', '/news/index', ['class' => 'btn btn-info']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
use app\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\models\Category;
use app\models\User;
use yii\bootstrap\ActiveForm;

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;

$langs = Lang::getActiveLangs();

$categories = [
    0 => 'Все'
];
$categories += Category::getCategories();

$hostname = Yii::$app->getUrlManager()->getHostInfo();

?>

<div class="row">
    <div class="option-panel">
        <?= Html::a('Добавить', Url::to('add'), ['class' => 'btn btn-success']) ?>
        <?= Html::a('Добавить несколько', Url::to('multi'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<div class="row">
    <div class="option-panel">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['news/index'],
            'options' => ['class' => 'form-inline'],
            'fieldConfig' => ['template' => '{label} {input}', 'inputOptions' => ['class' => 'form-control']],
            'enableClientValidation' => false
        ]); ?>

        <?= $form->field($model, 'id')->textInput(['placeholder' => 'ID', 'name' => 'id'])->label(false) ?>
        <?= $form->field($model, 'capt')->textInput(['placeholder' => 'Заголовок', 'name' => 'capt'])->label(false) ?>

        <?php if (Yii::$app->user->identity->role != User::ROLE_USER): ?>
            <?= $form->field($model, 'login')->textInput(['placeholder' => 'Логин', 'name' => 'login'])->label(false) ?>
        <?php endif; ?>

        <?= $form->field($model, 'id_category')->dropDownList($categories, ['name' => 'id_category'])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Найти', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="row">
    <div class="option-panel">
        <?php if ($news): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <th>Id</th>
                        <th width="250">Заголовок</th>
                        <th>Язык</th>
                        <th>Категория</th>
                        <th>Создано</th>
                        <th>Последнее изменение</th>
                        <th></th>
                    </tr>
                    <?php foreach ($news as $item): ?>
                        <tr>
                            <td><?= $item->id ?></td>
                            <td><a href="update/<?= $item->id ?>"><?= $item->capt ?></a></td>
                            <td><?= $langs[$item->lang_code] ?></td>
                            <td><?= (isset($categories[$item->id_category])) ? $categories[$item->id_category] : '-' ?></td>
                            <td>
                                <b><?= $item->user->login ?></b><br>
                                <?= date('d-m-Y H:i:s', $item->dt) ?>
                            </td>
                            <td>
                                <b><?= $item->lastUser->login ?></b><br>
                                <?= date('d-m-Y H:i:s', $item->dt_last) ?>
                            </td>
                            <td>
                                <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', [''], ['class' => 'option preview', 'title' => 'Просмотр', 'data-previewkey' => $item->id]) ?>
                                <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['news/update/' . $item->id], ['class' => 'option', 'title' => 'Редактировать']) ?>
           
                            </td>
                        </tr>
                        <tr id="preview<?= $item->id ?>" style="display: none;">
                            <td colspan="7"></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?= LinkPager::widget([
                'pagination' => $pages,
            ]); ?>
        <?php endif; ?>
    </div>
</div>
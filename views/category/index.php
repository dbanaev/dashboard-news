<?php
    use yii\helpers\Html;
    use yii\helpers\Url;

    $this->title = 'Категории';
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="option-panel">
        <?= Html::a('Добавить', Url::to('add'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<div class="row">
    <div class="option-panel">
        <?php if ($categories): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                        <tr>
                            <th>Id</th>
                            <th>Заголовок</th>
                            <th>Видимость</th>
                            <th></th>
                        </tr>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category->id ?></td>
                            <td><?= $category->title ?></td>
                            <td><?= ($category->visible) ? 'Видимая' : 'Невидимая' ?></td>
                            <td>
                                <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['category/update/' . $category->id], ['class' => 'option', 'title' => 'Редактировать']) ?>
                                <?= Html::a('<span class="glyphicon glyphicon-trash"></span>', ['category/delete/' . $category->id], ['class' => 'option', 'title' => 'Удалить', 'data-confirm' => 'Вы уверены, что хотите удалить данную категорию ?']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
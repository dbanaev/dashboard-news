<?php
    use app\models\User;
    use yii\helpers\Html;
    use yii\helpers\Url;

    $this->title = 'Пользователи';
    $this->params['breadcrumbs'][] = $this->title;

    $roles = User::getRoles();
?>

<div class="row">
    <div class="option-panel">
        <?= Html::a('Добавить', Url::to('add'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<div class="row">
    <div class="option-panel">
        <?php if ($users): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                        <tr>
                            <th>Id</th>
                            <th>Логин</th>
                            <th>Роль</th>
                            <th>Дата создания</th>
                            <th>Дата обновления</th>
                            <th></th>
                        </tr>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= $user->login ?></td>
                            <td><?= $roles[$user->role] ?></td>
                            <td><?= $user->created_at ?></td>
                            <td><?= $user->updated_at ?></td>
                            <td>
                                <?= Html::a('<span class="glyphicon glyphicon-share"></span>', ['user/enter/' . $user->id], ['class' => 'option', 'title' => 'Войти']) ?>
                                <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['user/update/' . $user->id], ['class' => 'option', 'title' => 'Редактировать']) ?>
                                <?= Html::a('<span class="glyphicon glyphicon-trash"></span>', ['user/delete/' . $user->id], ['class' => 'option', 'title' => 'Удалить', 'data-confirm' => 'Вы уверены, что хотите удалить данного пользователя ?']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['category/index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('form-category', [
    'model' => $model
]);

?>

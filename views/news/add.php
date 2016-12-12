<?php

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['news/index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('form-news', [
    'model' => $model
]);

?>

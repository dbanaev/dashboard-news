<?php

$this->title = 'Изменить';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['news/index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('form-news', [
    'model' => $model,
    'isMediadog' => $isMediadog
]);

?>

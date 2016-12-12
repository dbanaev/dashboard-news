<?php
use app\assets\DashboardAsset;
use yii\bootstrap\Html;
use app\components\AlertWidget;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use app\helpers\DashboardMenu;
use yii\widgets\Breadcrumbs;

DashboardAsset::register($this);
$this->beginPage();
?>

    <!doctype html>
    <html lang="<?= Yii::$app->language; ?>">
    <head>
        <?= Html::csrfMetaTags(); ?>
        <meta charset="<?= Yii::$app->charset; ?>">
        <?php $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']); ?>
        <title><?= Yii::$app->name; ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>

    <div class="wrap">
        <?php
        NavBar::begin([
            //'brandLabel' => 'ex-news.net',

        ]);

        $menuItems = DashboardMenu::getMenu();

        echo Nav::widget([
            'items' => $menuItems,
            'encodeLabels' => false,
            'options' => [
                'class' => 'navbar-nav navbar-right'
            ]
        ]);

        NavBar::end();
        ?>
        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'homeLink' => ['label' => '<span class="glyphicon glyphicon-home"></span>', 'url' => ['news/index']],
                'encodeLabels' => false,
            ]) ?>
            <?= AlertWidget::widget(); ?>
            <?= $content; ?>
        </div>
    </div>

    <footer class="footer"></footer>

    <?php $this->endBody(); ?>
    </body>
    </html>
<?php
$this->endPage();

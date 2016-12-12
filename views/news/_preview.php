<?php
    use yii\helpers\Html;

    $hostname = Yii::$app->getUrlManager()->getHostInfo();
?>


<?php $urlFull = $hostname . '/' . $item->url_full; ?>
<?php if ($isMediadog): ?>
    <?php $urlFull .= '?id=' . $item->id;  ?>
<?php endif; ?>
<p><?= Html::a($urlFull, $urlFull, ['target' => '_blank']) ?></p>

<?php $urlShort = $hostname . '/' . $item->url_short; ?>
<?php if ($isMediadog): ?>
    <?php $urlShort .= '?id=' . $item->id;  ?>
<?php endif; ?>
<p><?= Html::a($urlShort, $urlShort, ['target' => '_blank']) ?></p>

<p><?= Html::img('/dimg/' . $item->img_file, ['class' => 'thumbnail']) ?></p>

<div class="form-group">
    <textarea id="txt_full_<?= $item->id ?>" class="ckeditor form-control" rows="7" disabled><?= $item->txt_full ?></textarea>
</div>
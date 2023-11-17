<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BelongCity $model */

$this->title = '修改厅点';
$this->params['breadcrumbs'][] = ['label' => '厅点管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="belong-city-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

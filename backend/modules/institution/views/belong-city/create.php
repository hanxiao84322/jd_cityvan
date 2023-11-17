<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BelongCity $model */

$this->title = '新增厅点';
$this->params['breadcrumbs'][] = ['label' => '厅点管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="belong-city-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

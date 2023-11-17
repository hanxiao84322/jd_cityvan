<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderReply $model */

$this->title = 'Update Work Order Reply: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Work Order Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="work-order-reply-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

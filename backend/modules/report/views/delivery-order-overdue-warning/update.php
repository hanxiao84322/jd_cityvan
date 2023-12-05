<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderOverdueWarning $model */

$this->title = 'Update Delivery Order Overdue Warning: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Delivery Order Overdue Warnings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="delivery-order-overdue-warning-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

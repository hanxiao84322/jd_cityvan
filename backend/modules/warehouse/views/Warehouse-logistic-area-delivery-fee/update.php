<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WarehouseLogisticAreaDeliveryFee $model */

$this->title = 'Update Warehouse Logistic Area Delivery Fee: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Warehouse Logistic Area Delivery Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="warehouse-logistic-area-delivery-fee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

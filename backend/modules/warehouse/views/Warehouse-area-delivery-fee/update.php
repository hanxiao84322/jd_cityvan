<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WarehouseAreaDeliveryFee $model */

$this->title = 'Update Warehouse Area Delivery Fee: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Warehouse Area Delivery Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="warehouse-area-delivery-fee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

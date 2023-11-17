<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WarehouseLogisticAreaDeliveryFee $model */

$this->title = 'Create Warehouse Logistic Area Delivery Fee';
$this->params['breadcrumbs'][] = ['label' => 'Warehouse Logistic Area Delivery Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-logistic-area-delivery-fee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

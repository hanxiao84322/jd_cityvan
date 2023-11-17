<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrder $model */

$this->title = '修改订单号';
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="delivery-order-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryAdjustOrder $model */

$this->title = '修改订单调整单';
$this->params['breadcrumbs'][] = ['label' => '订单调整单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-adjust-order-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

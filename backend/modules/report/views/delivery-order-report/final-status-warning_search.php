<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-search">
    <div class="row">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数据统计条件为，发货时间大于<?= date('Y-m-d :i:s', strtotime('-20 day'))?>；运单状态不是：本人签收、代签收、拒收入库。</p>
    </div>
</div>

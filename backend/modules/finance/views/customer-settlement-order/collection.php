<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrder $model */

$this->title = '结算单确认收款';
$this->params['breadcrumbs'][] = ['label' => '结算单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '确认收款';
?>
<div class="customer-settlement-order-update">

    <?= $this->render('_form_collection', [
        'model' => $model,
    ]) ?>

</div>

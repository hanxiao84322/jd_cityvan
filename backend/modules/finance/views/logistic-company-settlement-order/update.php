<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrder $model */

$this->title = '修改结算单';
$this->params['breadcrumbs'][] = ['label' => '结算列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-settlement-order-update">

    <?= $this->render('_form', [
        'model' => $model,
        'logisticCompanyCheckBillModel' => $logisticCompanyCheckBillModel,
        'adjustTermList' => $adjustTermList
    ]) ?>

</div>

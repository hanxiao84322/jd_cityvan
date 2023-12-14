<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrder $model */

$this->title = '创建结算单';
$this->params['breadcrumbs'][] = ['label' => '结算单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-settlement-order-create">

    <?= $this->render('_form', [
        'model' => $model,
        'logisticCompanyCheckBillModel' => $logisticCompanyCheckBillModel,
    ]) ?>

</div>

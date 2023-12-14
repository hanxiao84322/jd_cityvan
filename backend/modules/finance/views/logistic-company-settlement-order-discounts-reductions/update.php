<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDiscountsReductions $model */

$this->title = '修改结算单优惠方案: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '结算单优惠方案列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-settlement-order-discounts-reductions-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDiscountsReductions $model */

$this->title = '创建结算单优惠方案';
$this->params['breadcrumbs'][] = ['label' => '结算单优惠方案列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-settlement-order-discounts-reductions-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

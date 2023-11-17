<?php

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrder $model */
/** @var int $level */
/** @var int $institutionId */

$this->title = '新增客户结算单';
$this->params['breadcrumbs'][] = ['label' => '客户结算单列表', 'url' => ['index']];
?>
<div class="customer-settlement-order-create">

    <?= $this->render('_form', [
        'model' => $model,
        'level' => $level,
        'institutionId' => $institutionId
    ]) ?>

</div>

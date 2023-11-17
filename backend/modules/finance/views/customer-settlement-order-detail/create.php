<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrderDetail $model */

$this->title = 'Create Customer Settlement Order Detail';
$this->params['breadcrumbs'][] = ['label' => 'Customer Settlement Order Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-settlement-order-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

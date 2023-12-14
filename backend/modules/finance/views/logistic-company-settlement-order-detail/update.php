<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDetail $model */

$this->title = 'Update Logistic Company Settlement Order Detail: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Settlement Order Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistic-company-settlement-order-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

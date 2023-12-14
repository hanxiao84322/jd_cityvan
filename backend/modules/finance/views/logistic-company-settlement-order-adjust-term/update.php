<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderAdjustTerm $model */

$this->title = 'Update Logistic Company Settlement Order Adjust Term: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Settlement Order Adjust Terms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistic-company-settlement-order-adjust-term-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

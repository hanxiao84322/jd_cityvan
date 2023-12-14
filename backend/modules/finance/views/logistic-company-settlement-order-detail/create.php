<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDetail $model */

$this->title = 'Create Logistic Company Settlement Order Detail';
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Settlement Order Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-settlement-order-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

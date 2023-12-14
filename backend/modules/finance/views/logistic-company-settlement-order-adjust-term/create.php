<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderAdjustTerm $model */

$this->title = 'Create Logistic Company Settlement Order Adjust Term';
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Settlement Order Adjust Terms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-settlement-order-adjust-term-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

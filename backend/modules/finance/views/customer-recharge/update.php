<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CustomerRecharge $model */

$this->title = 'Update Customer Recharge: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Recharges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-recharge-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

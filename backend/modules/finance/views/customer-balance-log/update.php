<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalanceLog $model */

$this->title = 'Update Customer Balance Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Balance Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-balance-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

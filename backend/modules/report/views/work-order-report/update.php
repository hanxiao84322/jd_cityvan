<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiency $model */

$this->title = 'Update Customer Service Daily Efficiency: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Service Daily Efficiencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-service-daily-efficiency-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiency $model */

$this->title = 'Create Customer Service Daily Efficiency';
$this->params['breadcrumbs'][] = ['label' => 'Customer Service Daily Efficiencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-service-daily-efficiency-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

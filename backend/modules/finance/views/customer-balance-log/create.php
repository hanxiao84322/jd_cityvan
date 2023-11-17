<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalanceLog $model */

$this->title = 'Create Customer Balance Log';
$this->params['breadcrumbs'][] = ['label' => 'Customer Balance Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-balance-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

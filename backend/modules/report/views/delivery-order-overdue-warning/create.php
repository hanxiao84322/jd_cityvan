<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderOverdueWarning $model */

$this->title = 'Create Delivery Order Overdue Warning';
$this->params['breadcrumbs'][] = ['label' => 'Delivery Order Overdue Warnings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-overdue-warning-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiencySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-service-daily-efficiency-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'work_order_create_num') ?>

    <?php // echo $form->field($model, 'work_order_deal_num') ?>

    <?php // echo $form->field($model, 'work_order_finished_num') ?>

    <?php // echo $form->field($model, 'work_order_not_finished_num') ?>

    <?php // echo $form->field($model, 'work_order_finished_rate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

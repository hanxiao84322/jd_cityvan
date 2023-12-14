<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDetailSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-settlement-order-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'settlement_order_no') ?>

    <?= $form->field($model, 'logistic_no') ?>

    <?= $form->field($model, 'warehouse_code') ?>

    <?= $form->field($model, 'logistic_id') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'size') ?>

    <?php // echo $form->field($model, 'size_weight') ?>

    <?php // echo $form->field($model, 'need_receipt_amount') ?>

    <?php // echo $form->field($model, 'finish_time') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

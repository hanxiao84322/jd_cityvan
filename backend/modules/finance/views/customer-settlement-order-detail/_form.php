<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrderDetail $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-settlement-order-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'settlement_order_no')->textInput() ?>

    <?= $form->field($model, 'logistic_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'institution_id')->textInput() ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'district')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'need_receipt_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

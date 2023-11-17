<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalance $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-balance-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'institution_id')->textInput() ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'face_orders_num')->textInput() ?>

    <?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_recharge_time')->textInput() ?>

    <?= $form->field($model, 'default_recharge_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_operation_detail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_recharge_notes')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

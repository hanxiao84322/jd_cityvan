<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\WarehouseLogisticAreaDeliveryFee $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="warehouse-logistic-area-delivery-fee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'warehouse_id')->textInput() ?>

    <?= $form->field($model, 'logistic_id')->textInput() ?>

    <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'district')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'follow_weight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'follow_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'agent_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_cancel')->textInput() ?>

    <?= $form->field($model, 'create_user')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_user')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiency $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-service-daily-efficiency-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'work_order_create_num')->textInput() ?>

    <?= $form->field($model, 'work_order_deal_num')->textInput() ?>

    <?= $form->field($model, 'work_order_finished_num')->textInput() ?>

    <?= $form->field($model, 'work_order_not_finished_num')->textInput() ?>

    <?= $form->field($model, 'work_order_finished_rate')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

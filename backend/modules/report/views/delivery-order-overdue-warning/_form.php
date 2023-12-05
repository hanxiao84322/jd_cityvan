<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderOverdueWarning $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-overdue-warning-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'warehouse_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'logistic_id')->textInput() ?>

    <?= $form->field($model, 'less_one_day')->textInput() ?>

    <?= $form->field($model, 'one_to_two_day')->textInput() ?>

    <?= $form->field($model, 'two_to_three_day')->textInput() ?>

    <?= $form->field($model, 'three_to_five_day')->textInput() ?>

    <?= $form->field($model, 'five_to_seven_day')->textInput() ?>

    <?= $form->field($model, 'more_seven_day')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

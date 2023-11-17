<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalanceLog $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-balance-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'institution_id')->textInput() ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'before_balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'after_balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'change_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'category')->textInput() ?>

    <?= $form->field($model, 'change_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

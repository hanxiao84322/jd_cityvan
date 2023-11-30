<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ApproveLog $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="approve-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_type')->textInput() ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'approve_status')->textInput() ?>

    <?= $form->field($model, 'approve_opinion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'approve_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'approve_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'approve_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

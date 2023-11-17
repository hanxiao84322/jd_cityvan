<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderTask $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file_path')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'result')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'apply_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'apply_time')->textInput() ?>

    <?= $form->field($model, 'finished_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

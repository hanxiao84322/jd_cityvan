<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ImportantCustomer $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="important-customer-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('姓名')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">

        <?= $form->field($model, 'phone', ['options' => ['class' => 'col-xs-3']])->label('电话')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">

        <?= $form->field($model, 'address', ['options' => ['class' => 'col-xs-3']])->label('地址')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">

        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('姓名')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">

        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('姓名')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">


    <?= $form->field($model, 'complaint_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'work_order_num')->textInput() ?>

    <?= $form->field($model, 'target')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'create_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'update_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

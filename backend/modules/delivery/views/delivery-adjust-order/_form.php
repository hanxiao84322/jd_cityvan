<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryAdjustOrder $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-adjust-order-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->label('快递单号')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'adjust_order_no', ['options' => ['class' => 'col-xs-3']])->label('调整单号')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'adjust_amount', ['options' => ['class' => 'col-xs-3']])->label('调整金额(元)')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\DeliveryAdjustOrder::$typeList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\DeliveryAdjustOrder::$statusList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'note',['options' => ['class' => 'col-xs-3']])->textarea(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?php
        echo $form->field($model, 'files[]', ['options' => ['class' => 'col-xs-4']])->fileInput(['multiple' => true])->label('上传附件');
        ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

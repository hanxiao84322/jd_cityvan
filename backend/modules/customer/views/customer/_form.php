<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Customer $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */
/** @var int $level */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?= $form->field($model, 'name', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?php if ($level == \backend\models\Institution::LEVEL_PARENT) {?>
        <?= $form->field($model, 'institution_id',['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\Institution::getAllById($institutionId, $level),'id', 'name'), ['prompt' => '---全选---'])->label('组织机构'); ?>
        <?php } else {?>
            <div class="form-group col-xs-4 field-customer-sender_name">
                <label class="control-label" for="customer-sender_name">组织机构</label>
                <input type="hidden" id="customer-sender_name" class="form-control input-sm" name="Customer[institution_id]" value="<?php echo $institutionId;?>" maxlength="10">
                <input type="text" id="customer-sender_name" class="form-control input-sm"  value="<?php echo \backend\models\Institution::getNameById($institutionId);?>" readonly maxlength="10">

                <div class="help-block"></div>
            </div>        <?php } ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'delivery_platform', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'sender_name', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'sender_phone', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'sender_company', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'sender_address', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'order_get_type', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'status', $options)->dropDownList(\common\models\Customer::$statusList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'code', $options)->textInput(['maxlength' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

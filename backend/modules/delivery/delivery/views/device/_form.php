<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Device $model */
/** @var yii\widgets\ActiveForm $form */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="device-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

    <?= $form->field($model, 'code', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">

        <?= $form->field($model, 'belong_city_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\BelongCity::getAll(),'id', 'name'), ['prompt' => '---全选---'])->label('选择厅点'); ?>
    </div>
    <div class="row">
    <?= $form->field($model, 'direction', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

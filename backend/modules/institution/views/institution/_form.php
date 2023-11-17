<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Institution $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $level */
/** @var int $institutionId */
$level = empty($model->level) ? $level + 1 : $model->level;
$id = empty($model->parent_id) ? $institutionId : $model->parent_id;
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="institution-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <?= $form->field($model, 'code', $options)->textInput(); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'name', $options)->textInput(); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'sort_name', $options)->textInput(); ?>
    </div>
    <input type="hidden" name="Institution[level]" value="<?= $level?>">
    <input type="hidden" name="Institution[parent_id]" value="<?= $id?>">

    </div>
    <div class="row" style="display: <?= (($level) == \backend\models\Institution::LEVEL_SUN) ? 'block' : 'none' ?>;">
        <?= $form->field($model, 'belong_city_list', ['options' => ['class' => 'col-xs-4']])->label('选择厅点')->checkboxList(\yii\helpers\ArrayHelper::map(\backend\models\BelongCity::getAll(),'id', 'name'), ['value'=>json_decode($model->belong_city_list, true)]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'phone', $options)->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'status', $options)->dropDownList(\backend\models\Institution::$statusList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'content', $options)->textarea(['style' => 'height:100px;', 'placeholder' => '输入简介']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

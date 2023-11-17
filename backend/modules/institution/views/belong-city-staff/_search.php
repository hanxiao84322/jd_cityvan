<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BelongCityStaffSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="belong-city-staff-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <?= $form->field($model, 'belong_city_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\BelongCity::getAll(),'id', 'name'), ['prompt' => '---全选---'])->label('选择厅点'); ?>

    <?= $form->field($model, 'code', ['options' => ['class' => 'col-xs-3']])->label('员工编码') ?>

    <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('员工名称') ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

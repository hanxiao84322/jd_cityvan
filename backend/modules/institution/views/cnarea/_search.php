<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CnareaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cnarea-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

    <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('名称') ?>
    <?= $form->field($model, 'area_code', ['options' => ['class' => 'col-xs-3']])->label('编码') ?>
    <?= $form->field($model, 'level', ['options' => ['class' => 'col-xs-3']])->label('等级')->dropDownList(\common\models\Cnarea::$levelList, ['prompt' => '---全部---']) ?>
    <?= $form->field($model, 'parent_code', ['options' => ['class' => 'col-xs-3']])->label('上级编码') ?>

    </div>


    <?php // echo $form->field($model, 'city_code') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'short_name') ?>

    <?php // echo $form->field($model, 'merger_name') ?>

    <?php // echo $form->field($model, 'pinyin') ?>

    <?php // echo $form->field($model, 'lng') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

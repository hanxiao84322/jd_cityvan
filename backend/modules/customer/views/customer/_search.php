<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */
/** @var int $level */

?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

        <?= $form->field($model, 'institution_id',['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\Institution::getAllById($institutionId, $level),'id', 'name'), ['prompt' => '---全选---'])->label('组织机构'); ?>
        <?= $form->field($model, 'sender_name', ['options' => ['class' => 'col-xs-3']])->label('寄件人姓名') ?>
        <?= $form->field($model, 'sender_phone', ['options' => ['class' => 'col-xs-3']])->label('寄件人联系电话') ?>
        <?= $form->field($model, 'sender_company', ['options' => ['class' => 'col-xs-3']])->label('寄件人公司') ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

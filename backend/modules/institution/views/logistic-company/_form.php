<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompany $model */
/** @var yii\widgets\ActiveForm $form */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="logistic-company-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

    <?= $form->field($model, 'company_name', $options)->textInput()->label('名称'); ?>
    </div>
    <div class="row">

    <?= $form->field($model, 'responsible_area', $options)->textInput()->label('负责区域'); ?>
    </div>
    <div class="row">

        <?= $form->field($model, 'status', $options)->label('状态')->dropDownList(\common\models\LogisticCompany::$statusList) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

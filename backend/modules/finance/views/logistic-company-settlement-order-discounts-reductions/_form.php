<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDiscountsReductions $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-settlement-order-discounts-reductions-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">

    <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanySettlementOrderDiscountsReductions::$typeList, ['prompt' => '-全部-', 'class'=>'form-control']); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'min_price', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
    </div>
<div class="row">
    <?= $form->field($model, 'discount', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
</div>
    <div class="row">
        <?= $form->field($model, 'sub_price', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
    <?= $form->field($model, 'content',['options' => ['class' => 'col-xs-3']])->textarea() ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDiscountsReductionsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-settlement-order-discounts-reductions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('方案名称') ?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanySettlementOrderDiscountsReductions::$typeList, ['prompt' => '-全部-', 'class'=>'form-control']); ?>

    </div>


    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

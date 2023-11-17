<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */
/** @var int $level */
?>

<div class="customer-settlement-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'settlement_order_no', ['options' => ['class' => 'col-xs-3']]) ?>
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->label('快递单号') ?>
    <?= $form->field($model, 'customer_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Customer::getAllByInstitutionId($institutionId, $level), 'id', 'name'), ['prompt' => '---全选---'])->label('客户'); ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

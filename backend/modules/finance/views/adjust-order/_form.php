<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\CustomerSettlementOrder;
\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var common\models\AdjustOrder $model */
/** @var yii\widgets\ActiveForm $form */

/** @var int $institutionId */
/** @var int $level */
?>

<div class="adjust-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'settlement_no', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(CustomerSettlementOrder::find()->where(['institution_id' => $institutionId, 'status' => CustomerSettlementOrder::STATUS_WAIT])->asArray()->all(), 'settlement_order_no', 'settlement_order_no'), ['prompt' => '---请选择---','class' => 'form-control select2'])->label('结算单'); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'adjust_amount', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\AdjustOrder::$typeList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\AdjustOrder::$statusList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'note', ['options' => ['class' => 'col-xs-3']])->textarea() ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>

    $(function () {
        $(".select2").select2({language: 'zh-CN'});
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

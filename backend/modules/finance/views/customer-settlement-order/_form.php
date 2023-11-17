<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrder $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $level */
/** @var int $institutionId */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="customer-settlement-order-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?= $form->field($model, 'institution_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\Institution::find()->where(['status' => \backend\models\Institution::STATUS_NORMAL])->asArray()->all(), 'id', 'name'), ['prompt' => '-全部-', 'class' => 'form-control select2']); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'customer_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Customer::getAllByInstitutionId($institutionId, $level), 'id', 'name'), ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'customer_id']); ?>
    </div>
    <div class="row">

    <?= $form->field($model, 'start_time',['options' => ['class' => 'col-xs-3']])->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => date('Y-m-d 00:00:00', time())],
        'value' => date('Y-m-d 00:00:00', time()),
        'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
    ])->label('开始时间');?>
    </div>
    <div class="row">
        <?= $form->field($model, 'end_time',['options' => ['class' => 'col-xs-3']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 23:59:59', time())],
            'value' => date('Y-m-d 23:59:59', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('结束时间');?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>

    $(function () {
        $(".select2").select2({language:'zh-CN'});
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-settlement-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'settlement_order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('结算单号') ?>
        <?= $form->field($model, 'logistic_company_check_bill_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('对账单号') ?>

        <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('快递公司'); ?>
        <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'code', 'name'), ['prompt' => '---全选---'])->label('仓库'); ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'create_time_start',['options' => ['class' => 'col-xs-3']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 00:00:00', time())],
            'value' => date('Y-m-d 00:00:00', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('创建时间开始');?>
        <?= $form->field($model, 'create_time_end',['options' => ['class' => 'col-xs-3']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 23:59:59', time())],
            'value' => date('Y-m-d 23:59:59', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('创建时间结束');?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\LogisticCompanySettlementOrder::$statusList, ['prompt' => '---全部---']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'onclick' => 'return searchForm();']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::button('导出', ['class' => 'btn btn-info', 'onclick' => 'return exportDataForm();', 'style' => 'margin-left:15px']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    <?php $this->beginBlock('js') ?>
    function exportDataForm() {
        $('#search_form').attr('action','/index.php/finance/logistic-company-settlement-order/export-data');
        $('#search_form').submit();
    }
    function searchForm() {
        $('#search_form').attr('action','/index.php/finance/logistic-company-settlement-order/index');
        $('#search_form').submit();
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>


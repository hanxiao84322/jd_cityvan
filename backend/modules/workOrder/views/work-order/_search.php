<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="work-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('快递单号') ?>
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('订单号') ?>
        <?= $form->field($model, 'work_order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('工单号') ?>
        <?= $form->field($model, 'jd_work_order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('京东工单号') ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'operate_username', ['options' => ['class' => 'col-xs-3']])->textInput()->label('负责人') ?>
        <?= $form->field($model, 'assign_username', ['options' => ['class' => 'col-xs-3']])->textInput()->label('指派人') ?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->label('类型')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\WorkOrderType::getAll(), 'id', 'name'), ['prompt' => '---全部---']) ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\WorkOrder::$statusList, ['prompt' => '---全部---']) ?>
    </div>
    <div class="row">
        <?php if (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_CUSTOMER_SERVICE) { ?>
            <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->label('仓库')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'code', 'name'), ['prompt' => '---全部---']) ?>
        <?php } ?>

        <?php if (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) { ?>
            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->label('快递公司')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全部---']) ?>
        <?php } ?>
        <?= $form->field($model, 'create_time_start', ['options' => ['class' => 'col-xs-3']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 00:00:00', time())],
            'value' => date('Y-m-d 00:00:00', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('开始时间');?>
        <?= $form->field($model, 'create_time_end', ['options' => ['class' => 'col-xs-3']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 23:59:59', time())],
            'value' => date('Y-m-d 23:59:59', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('结束时间');?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::button('导出', ['class' => 'btn btn-info', 'onclick' => 'return exportDataForm();', 'style' => 'margin-left:15px']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>
    function exportDataForm() {
        $('#search_form').attr('action','/index.php/workOrder/work-order/export-data');
        $('#search_form').submit();
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
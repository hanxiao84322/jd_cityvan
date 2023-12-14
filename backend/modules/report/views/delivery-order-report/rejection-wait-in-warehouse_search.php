<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\datetime\DateTimePicker;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['rejection-wait-in-warehouse'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('快递单号') ?>
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('京东单号') ?>
        <?php if (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) {?>
            <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'id', 'name'), ['prompt' => '---全选---'])->label('仓库'); ?>

            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('快递公司'); ?>
        <?php }?>
    </div>

    <div class="row">
        <?= $form->field($model, 'time_type', ['options' => ['class' => 'col-xs-3']])->label('时间类型')->dropDownList(\common\models\DeliveryOrder::$timeTypeList, ['prompt' => '---请选择---']) ?>
        <?= $form->field($model, 'create_time_start',['options' => ['class' => 'col-xs-3']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 00:00:00', time())],
            'value' => date('Y-m-d 00:00:00', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('开始时间');?>
        <?= $form->field($model, 'create_time_end',['options' => ['class' => 'col-xs-3']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => date('Y-m-d 23:59:59', time())],
            'value' => date('Y-m-d 23:59:59', time()),
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true]
        ])->label('结束时间');?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\DeliveryOrder::$statusList, ['prompt' => '---全部---']) ?>


    </div>
    <div class="row">
        <?= $form->field($model, 'receiver_phone', ['options' => ['class' => 'col-xs-3']])->textInput()->label('客户电话') ?>

        <?= $form->field($model, 'is_deduction', ['options' => ['class' => 'col-xs-3']])->label('是否扣款')->dropDownList(\common\models\DeliveryOrder::$yesOrNotList, ['prompt' => '---全部---']) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'onclick' => 'return searchForm();']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?php if (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) {?>
        <?php }?>

    </div>

    <?php ActiveForm::end(); ?>

</div>


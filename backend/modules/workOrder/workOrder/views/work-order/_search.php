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
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('订单号') ?>
        <?= $form->field($model, 'work_order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('工单号') ?>
        <?= $form->field($model, 'operate_username', ['options' => ['class' => 'col-xs-3']])->textInput()->label('负责人') ?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->label('类型')->dropDownList(\common\models\WorkOrder::$typeList, ['prompt' => '---全部---']) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'priority', ['options' => ['class' => 'col-xs-3']])->label('优先级')->dropDownList(\common\models\WorkOrder::$priorityList, ['prompt' => '---全部---']) ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\WorkOrder::$statusList, ['prompt' => '---全部---']) ?>


    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

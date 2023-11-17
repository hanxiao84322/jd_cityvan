<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryAdjustOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-adjust-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('邮政单号') ?>
        <?= $form->field($model, 'adjust_order_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('订单调整单号') ?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->label('类型')->dropDownList(\common\models\DeliveryAdjustOrder::$typeList, ['prompt' => '---全部---']) ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\DeliveryAdjustOrder::$statusList, ['prompt' => '---全部---']) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderOverdueWarningSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-overdue-warning-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

        <?= $form->field($model, 'create_time_start', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_start']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_time_start,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm-dd', 'minView' => 2]
        ])->label('开始日期'); ?>
        <?= $form->field($model, 'create_time_end', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_end']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_time_end,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm-dd', 'minView' => 2]])->label('结束日期'); ?>
        <?php if (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) {?>
            <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'code', 'name'), ['prompt' => '---全选---'])->label('仓库'); ?>

            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('快递公司'); ?>
        <?php }?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>

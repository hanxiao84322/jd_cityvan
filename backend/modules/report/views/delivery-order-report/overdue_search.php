<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="work-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['overdue'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->label('仓库')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(),'code', 'name'), ['prompt' => '---全部---']) ?>
        <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->label('快递公司')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(),'id', 'company_name'), ['prompt' => '---全部---']) ?>
        <?= $form->field($model, 'create_month', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_start']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_month,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm', 'minView' => 2]
        ])->label('月份'); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

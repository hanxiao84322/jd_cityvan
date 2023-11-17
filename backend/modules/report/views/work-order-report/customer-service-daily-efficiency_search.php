<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiencySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-service-daily-efficiency-search">

    <?php $form = ActiveForm::begin([
        'action' => ['customer-service-daily-efficiency'],
        'method' => 'get',
        'options' => ['onsubmit' => 'return checkDayDiff();']
    ]); ?>
    <div class="row">

        <?= $form->field($model, 'create_time_start', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_start']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_time_start,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm-dd', 'minView' => 2]
        ])->label('开始日期'); ?>
        <?= $form->field($model, 'create_time_end', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_end']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_time_end,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm-dd', 'minView' => 2]])->label('结束日期'); ?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->label('类型')->dropDownList(\backend\models\UserBackend::$typeList, ['prompt' => '---全部---']) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>
    // $(function () {
    //     document.getElementById('w0').addEventListener('submit', function(event) {
    //         var startDate = new Date(document.getElementById('customerservicedailyefficiencysearch-create_time_start').value);
    //         var endDate = new Date(document.getElementById('customerservicedailyefficiencysearch-create_time_end').value);
    //         var timeDiff = Math.abs(endDate.getTime() - startDate.getTime());
    //         var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
    //
    //         if (diffDays > 30) {
    //             alert('时间区间超过30天，请重新选择。');
    //             event.preventDefault();
    //         }
    //     });
    // });


    function checkDayDiff() {
        var create_time_start = $('#customerservicedailyefficiencysearch-create_time_start').val();
        var create_time_end = $('#customerservicedailyefficiencysearch-create_time_end').val();
        var date1 = new Date(create_time_start);
        var date2 = new Date(create_time_end);

// 将日期转换为以毫秒为单位的时间戳，并计算时间戳的差值
        var timeDiff = Math.abs(date2.getTime() - date1.getTime());

// 计算天数差异
        var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

        if (daysDiff > 30) {
            alert('时间区间不能超过30天');
            return false;
        }
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

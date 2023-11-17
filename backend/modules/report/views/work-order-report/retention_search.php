<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiencySearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $totalNum */

?>

<div class="customer-service-daily-efficiency-search">

    <?php $form = ActiveForm::begin([
        'action' => ['retention'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\backend\models\UserBackend::$staffTypeList, ['prompt' => '---全部---']) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="row">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;未完成总量计数项：共<?= $totalNum;?>条
    </div>
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

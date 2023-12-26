<?php

use common\models\DeliveryOrderTask;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\datetime\DateTimePicker;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-check-bill-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>

    <div class="row">
        <?= $form->field($model, 'logistic_company_check_bill_no', ['options' => ['class' => 'col-xs-3']])->textInput()->label('对账单号') ?>
        <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'code', 'name'), ['prompt' => '---全选---'])->label('仓库'); ?>
            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('快递公司'); ?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->label('类型')->dropDownList(\common\models\LogisticCompanyCheckBill::$typeList, ['prompt' => '---全部---']) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'create_time_start', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_start']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_time_start,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm-dd', 'minView' => 2]
        ])->label('开始日期'); ?>
        <?= $form->field($model, 'create_time_end', ['options' => ['class' => 'col-xs-3', 'id' => 'create_time_end']])->widget(\kartik\datetime\DateTimePicker::classname(), [
            'value' => $model->create_time_end,
            'pluginOptions' => ['autoclose' => true, 'todayHighlight' => true, 'todayBtn' => true, 'format' => 'yyyy-mm-dd', 'minView' => 2]])->label('结束日期'); ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\LogisticCompanyCheckBill::$statusList, ['prompt' => '---全部---']) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'onclick' => 'return searchForm();']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::button('导出', ['class' => 'btn btn-info', 'onclick' => 'return exportDataForm();', 'style' => 'margin-left:15px']) ?>
            <?= Html::a('导入生成', '#', [
                'class' => 'btn btn-success',
                'data-toggle' => 'modal',
                'data-target' => '#page-modal'    //此处对应Modal组件中设置的id
            ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

Modal::begin([
    'id' => 'page-modal',
    'header' => '<h5>导入生成</h5>',
]);
?>
<p>上传文件，只允许上传.xls,.xlsx,.csv文件。</p>
<p>

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form'],
    ]); ?>
<div style="width: 50%; height: 100px;">    对账单类型：<?= Html::dropDownList('type', '', [\common\models\LogisticCompanyCheckBill::TYPE_PAY => \common\models\LogisticCompanyCheckBill::$typeList[\common\models\LogisticCompanyCheckBill::TYPE_PAY], \common\models\LogisticCompanyCheckBill::TYPE_REC => \common\models\LogisticCompanyCheckBill::$typeList[\common\models\LogisticCompanyCheckBill::TYPE_REC]],['class' => 'form-control', 'id' => 'order_type']) ?>
</div>
    <?= Html::fileInput('file', '', ['id' => 'file_input']) ?>
    <?php ActiveForm::end(); ?>
</p>
<p><?= Html::a('模板下载', ['download-template'], ['class' => 'btn btn-success']) ?>
</p>
<p><?= Html::button('导入', ['class' => 'btn btn-primary', 'id' => 'upload-btn']) ?>&nbsp;&nbsp;&nbsp;<?= Html::button('导入（后台执行）', ['class' => 'btn btn-primary', 'id' => 'task-create']) ?>&nbsp;&nbsp;&nbsp;<?= Html::button('关闭', ['class' => 'btn btn-close', 'id' => 'close-btn']) ?></p>

<p style="margin-top: 20px" id="message">
</p>
<div id="upload-result"
     style="height:220px;overflow:auto;overflow-x:hidden;border:1px solid #ccc;padding:5px"></div>
<?php
Modal::end();

?>
<script>
    <?php $this->beginBlock('js') ?>
    $(function () {
        $('#task-create').click(function () {
            const btn = $('#task-create');
            const show = $('#message');
            const fileInput = $('#file_input').get(0);
            const orderType = $('#order_type').val();
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('type', <?php echo DeliveryOrderTask::TYPE_CHECK_BILL;?>);
            formData.append('order_type', orderType);
            $.ajax({
                url: '/delivery/delivery-order-task/ajax-create',
                cache: false,
                dataType: 'json',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    btn.html('<i class="fa fa-refresh fa-spin"></i> 导入数据中，请勿关闭页面');
                    btn.attr('disabled', true);
                    show.html('');
                },
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.css('color', 'red');
                    } else {
                        show.css('color', 'green');
                    }
                    show.html(result.msg);
                    btn.html('导入数据（后台执行）');
                    btn.attr('disabled', false);
                }
            });
        });
        $('#upload-btn').click(function () {
            const btn = $('#upload-btn');
            const show = $('#upload-result');
            const fileInput = $('#file_input').get(0);
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            $.ajax({
                url: '/finance/logistic-company-check-bill/ajax-batch-update',
                cache: false,
                dataType: 'json',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    btn.html('<i class="fa fa-refresh fa-spin"></i> 导入数据中，请勿关闭页面');
                    btn.attr('disabled', true);
                    show.html('');
                },
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.css('color', 'red');
                        show.html(result.errorList);
                    } else {
                        // 显示导入结果
                        show.css('color', 'red');

                        var tips = '<div style="margin-bottom:5px;font-weight: bold">' +
                            '<span style="color:green">导入成功:' + result.successCount + '</span>&nbsp;&nbsp;' +
                            '<span style="color:red">导入失败:' + result.errorCount + '</span></div>' +
                            '<div style="margin-bottom:5px;font-weight: bold">' + result.errorList + '</div>';
                        show.html(tips);
                    }
                    btn.html('导入数据');
                    btn.attr('disabled', false);
                }
            });
        });
        $('#close-btn').click(function () {
            location.reload(true);
        });
    });

    function exportDataForm() {
        $('#search_form').attr('action','/index.php/finance/logistic-company-check-bill/export-data');
        $('#search_form').submit();
    }
    function searchForm() {
        $('#search_form').attr('action','/index.php/finance/logistic-company-check-bill/index');
        $('#search_form').submit();
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

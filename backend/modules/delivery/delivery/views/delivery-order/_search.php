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
        'action' => ['index'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('邮政单号') ?>
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('京东单号') ?>
        <?php if (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) {?>
            <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'id', 'name'), ['prompt' => '---全选---'])->label('仓库'); ?>

            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('物流公司'); ?>
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

            <?= Html::button('导出', ['class' => 'btn btn-info', 'onclick' => 'return exportDataForm();', 'style' => 'margin-left:15px']) ?>

            <?= Html::a('批量更新', '#', [
                'class' => 'btn btn-success',
                'data-toggle' => 'modal',
                'data-target' => '#page-modal'    //此处对应Modal组件中设置的id
            ]) ?>
        <?php }?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

Modal::begin([
    'id' => 'page-modal',
    'header' => '<h5>批量更新</h5>',
]);
?>
<p>上传文件，只允许上传.xls,.xlsx,.csv文件。</p>
<p>

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form'],
    ]); ?>
    <?= Html::fileInput('file', '', ['id' => 'file_input']) ?>
    <?php ActiveForm::end(); ?>
</p>
<p><?= Html::a('模板下载', ['download-template'], ['class' => 'btn btn-success']) ?>
</p>
<p><?= Html::button('导入数据', ['class' => 'btn btn-primary', 'id' => 'upload-btn']) ?>&nbsp;&nbsp;&nbsp;<?= Html::button('导入数据（后台执行）', ['class' => 'btn btn-primary', 'id' => 'task-create']) ?>&nbsp;&nbsp;&nbsp;<?= Html::button('关闭', ['class' => 'btn btn-close', 'id' => 'close-btn']) ?></p>

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
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
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
                url: '/delivery/delivery-order/ajax-batch-update',
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

    function exportForm() {
        $('#search_form').attr('action','/index.php/delivery/delivery-order/export');
        $('#search_form').submit();
    }
    function exportDataForm() {
        $('#search_form').attr('action','/index.php/delivery/delivery-order/export-data');
        $('#search_form').submit();
    }
    function searchForm() {
        $('#search_form').attr('action','/index.php/delivery/delivery-order/index');
        $('#search_form').submit();
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>


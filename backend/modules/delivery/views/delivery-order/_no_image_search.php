<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\datetime\DateTimePicker;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */
/** @var int $level */
?>

<div class="delivery-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">

        <?= $form->field($model, 'institution_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\Institution::getAllById($institutionId, $level), 'id', 'name'), ['prompt' => '---全选---']); ?>
        <?= $form->field($model, 'customer_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Customer::getAllByInstitutionId($institutionId, $level), 'id', 'name'), ['prompt' => '---全选---'])->label('客户'); ?>
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('快递单号') ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'onclick' => 'return searchForm();']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::button('导出', ['class' => 'btn btn-info', 'onclick' => 'return exportDataForm();', 'style' => 'margin-left:15px']) ?>
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
    <?= Html::fileInput('file') ?>
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

            var btn = $('#task-create');
            var show = $('#message');
            $.post({
                url: '/delivery/delivery-order-task/ajax-create',
                cache: false,
                dataType: 'json',
                data: new FormData($('#upload-form')[0]),
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
            var btn = $('#upload-btn');
            var show = $('#upload-result');
            $.post({
                url: '/delivery/delivery-order/ajax-batch-update',
                cache: false,
                dataType: 'json',
                data: new FormData($('#upload-form')[0]),
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
        $('#search_form').attr('action','/delivery/delivery-order/export');
        $('#search_form').submit();
    }
    function exportDataForm() {
        $('#search_form').attr('action','/delivery/delivery-order/export-data');
        $('#search_form').submit();
    }
    function searchForm() {
        $('#search_form').attr('action','/delivery/delivery-order/index');
        $('#search_form').submit();
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>


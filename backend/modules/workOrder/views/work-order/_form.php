<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="work-order-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->label('快递单号')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->label('订单号')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\WorkOrderType::getAll(), 'id', 'name'), ['prompt' => '---全选---']); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'priority', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\WorkOrder::$priorityList); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'receive_name', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <?= $form->field($model, 'receive_phone', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>
</div>
<div class="row">
    <?= $form->field($model, 'receive_address', ['options' => ['class' => 'col-xs-4']])->textInput(['maxlength' => true]) ?>
</div>
<div class="row">
    <?= $form->field($model, 'logistic_company', ['options' => ['class' => 'col-xs-4']])->textInput(['maxlength' => true])->label('快递公司'); ?>
</div>
<div class="row">
    <?= $form->field($model, 'order_create_num', ['options' => ['class' => 'col-xs-4']])->textInput(['maxlength' => true])->label('订单已创建工单数量'); ?>
</div>
<div class="row">
    <?= $form->field($model, 'customer_attention_level', ['options' => ['class' => 'col-xs-4']])->textInput(['maxlength' => true])->label('客户关注等级'); ?>
</div>
<div class="row">
    <?= $form->field($model, 'jd_work_order_no', ['options' => ['class' => 'col-xs-4']])->textInput(['maxlength' => true])->label('京东工单号'); ?>
</div>
<div class="row">
    <?= $form->field($model, 'penalty_amount', ['options' => ['class' => 'col-xs-4']])->textInput(['maxlength' => true])->label('罚款金额'); ?>
</div>
<div class="row">
    <?= $form->field($model, 'description', ['options' => ['class' => 'col-xs-4']])->textarea(['maxlength' => true])->label('工单说明'); ?>
</div>
<div class="row">
    <?php
    echo $form->field($model, 'files[]', ['options' => ['class' => 'col-xs-4']])->fileInput(['multiple' => true])->label('上传附件');
//    echo $form->field($model, 'file_path', ['options' => ['class' => 'col-xs-4']])->widget(FileInput::classname(), [
//        'options' => ['multiple' => true],
//        // 最少上传的文件个数限制
//        'pluginOptions' => [
//            'minFileCount' => 1,
//            // 最多上传的文件个数限制,需要配置`'multiple'=>true`才生效
//            'maxFileCount' => 10,
//            // 是否显示移除按钮，指input上面的移除按钮，非具体图片上的移除按钮
//            'showRemove' => true,
//            'showUpload' => false,
//            //是否显示[选择]按钮,指input上面的[选择]按钮,非具体图片上的上传按钮
//            'showBrowse' => true,
//            // 展示图片区域是否可点击选择多文件
//            'browseOnZoneClick' => true,
//        ],
//
//    ]);
?>
</div>
<div class="form-group">
    <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>

    $(function () {
        $(".select2").select2({language: 'zh-CN'});
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
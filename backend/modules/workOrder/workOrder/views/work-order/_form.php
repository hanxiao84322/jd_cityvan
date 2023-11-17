<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="work-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->label('订单号')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\WorkOrder::$typeList); ?>
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
    <?= $form->field($model, 'operate_username', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\UserBackend::find()->where(['status' => 1])->asArray()->all(), 'username', 'username'), ['prompt' => '---请选择---', 'class' => 'form-control select2'])->label('负责人'); ?>
</div>

<div class="row">
    <?= $form->field($model, 'description', ['options' => ['class' => 'col-xs-4']])->textarea(['maxlength' => true]) ?>
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
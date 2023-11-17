<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var common\models\BaseCost $model */
/** @var yii\widgets\ActiveForm $form */
if (empty($model->month)) {
    $model->month = date('Y-m', time());
}
?>

<div class="base-cost-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'warehouse', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\BaseCost::$warehouseList, ['prompt' => '-全部-', 'class' => 'form-control select2'])->label('集货仓库'); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'month', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'data_service_fee', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'month_rent', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'worker_num', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'worker_fee', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

    </div>
    <div class="row">
        <?= $form->field($model, 'device_fee', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true]) ?>

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
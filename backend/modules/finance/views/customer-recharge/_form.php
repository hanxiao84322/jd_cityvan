<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var backend\models\CustomerRecharge $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $level */
/** @var int $institutionId */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
$model->customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
?>

<div class="customer-recharge-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form'],
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'customer_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Customer::getAllByInstitutionId($institutionId, $level), 'id', 'name'), ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'customer_id']); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'amount', $options)->textInput(); ?>
    </div>
    <div class="row">

    <?= $form->field($model, 'notes', $options)->textarea(['maxlength' => true]) ?>
    </div>
    <div class="row">
    <?= $form->field($model, 'pay_image_path', $options)->fileInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
    <?= $form->field($model, 'invoice_image_path', $options)->fileInput(['maxlength' => true]) ?>
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

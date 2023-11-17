<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrder $model */
/** @var yii\widgets\ActiveForm $form */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="customer-settlement-order-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form'],
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'pay_image_path', $options)->fileInput(['maxlength' => true])->label('上传支付凭证'); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('确认收款', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

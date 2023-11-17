<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryImage $model */
/** @var yii\widgets\ActiveForm $form */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
$this->title = '手动解析';
$this->params['breadcrumbs'][] = ['label' => '运单图片解析', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="delivery-image-form">

    <?php $form = ActiveForm::begin([
        'action' => ['update-receiver-info-by-image'],
        'method' => 'post',
    ]); ?>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">快递单号</label>
            <input type="text" id="customer-sender_name" class="form-control input-sm" name="logistic_no" value="<?= $deliveryImageData['logistic_no']?>" maxlength="100">
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('解析', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">解析姓名</label>
            <?= $deliveryImageData['name']?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">解析电话</label>
            <?= $deliveryImageData['phone']?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">图片base46</label>
            <textarea  class="form-control input-sm" READONLY><?= $deliveryImageData['image_base64']?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">面单照片</label>
            <a href="<?= $deliveryImageData['file_path']?>"><img src="<?= $deliveryImageData['file_path']?>"></a>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">解析文本</label>
            <?= $deliveryImageData['text']?>

        </div>
    </div>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryImage $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $deliverySteps */

$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
$this->title = '物流轨迹查询';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="delivery-image-form">

    <?php $form = ActiveForm::begin([
        'action' => ['zjs-delivery-info'],
        'method' => 'post',
    ]); ?>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">快递单号</label>
            <input type="text" id="customer-sender_name" class="form-control input-sm" name="order_no" value="" maxlength="100">
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('解析', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="customer-view">
        <div class="row">
            <div class="row">
                <?= \common\components\LayoutHelper::boxBegin('物流轨迹') ?>
                <table class="table table-bordered " style="width:80%">
                    <tr>
                        <th><b>时间</b></th>
                        <th><b>轨迹</b></th>
                    </tr>
                    <?php foreach ($deliverySteps as $deliveryStep) {?>
                        <tr>
                            <td><b><?= $deliveryStep['operationTime']?></b></td>
                            <td><b><?= $deliveryStep['operationDescribe']?></b></td>
                        </tr>
                    <?php }?>
                </table>
                <?= \common\components\LayoutHelper::boxEnd() ?>
            </div>

        </div>

    </div>

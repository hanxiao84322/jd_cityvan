<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrder $model */
/** @var yii\widgets\ActiveForm $form */
$this->title = '上传面单';
$this->params['breadcrumbs'][] = ['label' => '运单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '上传';
?>

<div class="delivery-order-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="row">
        <div class="form-group col-xs-4">
            <label class="control-label" for="belongcity-name">名称</label>
            <input type="file" id="belongcity-name" class="form-control input-sm" name="file" aria-required="true" aria-invalid="true">
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('上传', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

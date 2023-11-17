<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['batch-update-status'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'logistic_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('邮政单号') ?>
        <?= $form->field($model, 'order_no', ['options' => ['class' => 'col-xs-3']])->textarea()->label('京东单号') ?>
        <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('物流公司'); ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'onclick' => 'return searchForm();']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ImportantCustomerSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="important-customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->textInput()->label('姓名') ?>
        <?= $form->field($model, 'phone', ['options' => ['class' => 'col-xs-3']])->textInput()->label('电话') ?>
        <?= $form->field($model, 'level', ['options' => ['class' => 'col-xs-3']])->label('类型')->dropDownList(\common\models\ImportantCustomer::$levelList, ['prompt' => '---全部---']) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="work-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->textInput()->label('仓库编码') ?>
        <?= $form->field($model, 'operate_username', ['options' => ['class' => 'col-xs-3']])->textInput()->label('系统客服') ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\WorkOrder::$statusList, ['prompt' => '---全部---']) ?>


    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

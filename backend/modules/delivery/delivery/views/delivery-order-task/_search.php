<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderTaskSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

    <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\DeliveryOrderTask::$statusList, ['prompt' => '---全部---']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

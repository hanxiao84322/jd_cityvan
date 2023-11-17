<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderReply $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="work-order-reply-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'work_order_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reply_content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'reply_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reply_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

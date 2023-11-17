<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryImage $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-image-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'logistic_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

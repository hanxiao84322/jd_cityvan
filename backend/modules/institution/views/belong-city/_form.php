<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BelongCity $model */
/** @var yii\widgets\ActiveForm $form */
$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
?>

<div class="belong-city-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <?= $form->field($model, 'name', $options)->textInput()->label('名称'); ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'status', $options)->dropDownList(\backend\models\BelongCity::$statusList, ['prompt' => '---全部---']); ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

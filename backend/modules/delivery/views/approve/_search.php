<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ApproveLogSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="approve-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_type') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'approve_status') ?>

    <?= $form->field($model, 'approve_opinion') ?>

    <?php // echo $form->field($model, 'approve_username') ?>

    <?php // echo $form->field($model, 'approve_name') ?>

    <?php // echo $form->field($model, 'approve_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

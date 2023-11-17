<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalanceLogSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="customer-balance-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'institution_id') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'before_balance') ?>

    <?= $form->field($model, 'after_balance') ?>

    <?php // echo $form->field($model, 'change_amount') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'category') ?>

    <?php // echo $form->field($model, 'change_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\vSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="warehouse-logistic-area-delivery-fee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'warehouse_id') ?>

    <?= $form->field($model, 'logistic_id') ?>

    <?= $form->field($model, 'province') ?>

    <?= $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'follow_weight') ?>

    <?php // echo $form->field($model, 'follow_price') ?>

    <?php // echo $form->field($model, 'return_rate') ?>

    <?php // echo $form->field($model, 'agent_rate') ?>

    <?php // echo $form->field($model, 'is_cancel') ?>

    <?php // echo $form->field($model, 'create_user') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_user') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

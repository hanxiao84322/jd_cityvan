<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderAdjustTerm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-settlement-order-adjust-term-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'settlement_order_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

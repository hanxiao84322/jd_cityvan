<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBill $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-check-bill-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <?= $form->field($model, 'note', ['options' => ['class' => 'col-xs-3']])->textarea(['rows' => 6]) ?>
    </div>
    <div class="row">
    <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanyCheckBill::$statusList, ['prompt' => '-全部-', 'class' => 'form-control'])->label('状态'); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

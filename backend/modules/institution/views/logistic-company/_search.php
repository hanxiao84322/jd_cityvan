<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'company_name', ['options' => ['class' => 'col-xs-3']])->label('快递公司名称') ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\LogisticCompany::$statusList, ['prompt' => '---全部---']) ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

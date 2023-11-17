<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\UserBackendSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-backend-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <?= $form->field($model, 'username', ['options' => ['class' => 'col-xs-3']])->label('用户名') ?>
        <?= $form->field($model, 'email', ['options' => ['class' => 'col-xs-3']])->label('邮箱') ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\backend\models\Institution::$statusList, ['prompt' => '---全部---']) ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['signup'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

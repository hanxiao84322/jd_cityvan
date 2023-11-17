<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\BaseCostSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="base-cost-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <?= $form->field($model, 'warehouse', ['options' => ['class' => 'col-xs-3']])->label('仓库') ?>
        <?= $form->field($model, 'month', ['options' => ['class' => 'col-xs-3']])->label('月份') ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

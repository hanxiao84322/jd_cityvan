<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\InstitutionSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $isParent */
?>

<div class="institution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'name', ['options' => ['class' => 'col-xs-3']])->label('名称') ?>
        <?= $form->field($model, 'code', ['options' => ['class' => 'col-xs-3']])->label('编码') ?>
        <?php if ($isParent) {?>
        <?= $form->field($model, 'level', ['options' => ['class' => 'col-xs-3']])->dropDownList(\backend\models\Institution::$levelList, ['prompt' => '---全选---']); ?>
        <?php } ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\backend\models\Institution::$statusList, ['prompt' => '---全部---']) ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

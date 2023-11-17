<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\UserBackend $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */
/** @var int $level */
?>

<div class="user-backend-form">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(); ?>
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            <?= $form->field($model, 'username')->label('登陆名')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'name')->label('姓名')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'phone')->label('电话')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'type')->label('类型')->dropDownList(\backend\models\UserBackend::$typeList, ['prompt' => '---全选---']); ?>

            <?= $form->field($model, 'warehouse_code_list')->label('选择仓库')->checkboxList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(),'code', 'name'), ['value'=>json_decode($model->warehouse_code_list, true)]) ?>
            <?= $form->field($model, 'logistic_id_list')->label('选择快递公司')->checkboxList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(),'id', 'company_name'), ['value'=>json_decode($model->logistic_id_list, true)]) ?>

            <?= $form->field($model, 'email')->label('邮箱') ?>
            <div class="form-group">
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

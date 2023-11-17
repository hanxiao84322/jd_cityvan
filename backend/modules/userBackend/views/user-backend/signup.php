<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model SignupForm */

/** @var int $institutionId */
/** @var int $level */

use backend\models\SignupForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '新增用户';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            <?= $form->field($model, 'username')->label('登陆名')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'name')->label('姓名')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'phone')->label('电话')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'type')->label('类型')->dropDownList(\backend\models\UserBackend::$typeList, ['prompt' => '---全选---']); ?>

            <?= $form->field($model, 'warehouse_code_list')->label('选择仓库')->checkboxList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(),'code', 'name'), ['value'=>!empty($model->warehouse_code_list) ? json_decode($model->warehouse_code_list, true) : '']) ?>
            <?= $form->field($model, 'logistic_id_list')->label('选择快递公司')->checkboxList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(),'id', 'company_name'), ['value'=>!empty($model->logistic_id_list) ? json_decode($model->warehouse_code_list, true) : '']) ?>

            <?= $form->field($model, 'email')->label('邮箱') ?>
            <?= $form->field($model, 'password')->label('密码')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton('添加', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
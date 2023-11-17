<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\UserBackend $model */
/** @var int $institutionId */
/** @var int $level */
/** @var string $password */
$this->title = '重置密码';
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
$model->password = $password;
?>
<div class="user-backend-update">
    <div class="user-backend-form">

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'password')->label('密码')->textInput(['autofocus' => true]) ?>
                <div class="form-group">
                    <?= Html::submitButton('重置', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

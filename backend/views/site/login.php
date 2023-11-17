<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = '登录';

$fieldOptions1 = [
    'options' => ['class' => 'form-group col-xs-4'],
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group col-xs-4'],
];
?>

<div class="login-box">
    <p><div class="login-logo">
        <b><?= Yii::$app->name?></b>
    </div></p>

    <!-- /.login-logo -->
    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
        <div class="row">

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
        </div>
        <div class="row">

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
        </div>

            <div class="row">
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>



    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->

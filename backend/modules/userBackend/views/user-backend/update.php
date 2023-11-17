<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserBackend $model */
$this->title = '修改用户';
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="user-backend-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

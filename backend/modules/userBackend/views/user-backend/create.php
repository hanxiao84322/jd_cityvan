<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserBackend $model */

$this->title = '新增用户';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-backend-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Institution $model */
/** @var int $level */
/** @var int $institutionId */
$this->title = '修改组织机构';
$this->params['breadcrumbs'][] = ['label' => '组织机构管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="institution-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'level' => $level,
        'institutionId' => $institutionId
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Institution $model */
/** @var int $level */
/** @var int $institutionId */
$this->title = '新增组织机构';
$this->params['breadcrumbs'][] = ['label' => '组织机构管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="institution-create">
    <?= $this->render('_form', [
        'model' => $model,
        'level' => $level,
        'institutionId' => $institutionId
    ]) ?>

</div>

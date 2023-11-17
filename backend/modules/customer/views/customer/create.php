<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Customer $model */
/** @var int $institutionId */
/** @var int $level */
$this->title = '新增客户';
$this->params['breadcrumbs'][] = ['label' => '客户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">
    <?= $this->render('_form', [
        'model' => $model,
        'institutionId' => $institutionId,
        'level' => $level
    ]) ?>

</div>

<?php

/** @var yii\web\View $this */
/** @var common\models\AdjustOrder $model */

$this->title = '新增调整单';
$this->params['breadcrumbs'][] = ['label' => '调整单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/** @var int $institutionId */
/** @var int $level */
?>
<div class="adjust-order-create">
    <?= $this->render('_form', [
        'model' => $model,
        'level' => $level,
        'institutionId' => $institutionId
    ]) ?>

</div>

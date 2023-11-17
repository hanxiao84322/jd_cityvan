<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\AdjustOrder $model */
/** @var int $institutionId */
/** @var int $level */

$this->title = '修改调整单';
$this->params['breadcrumbs'][] = ['label' => '调整单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="adjust-order-update">

    <?= $this->render('_form', [
        'model' => $model,
        'level' => $level,
        'institutionId' => $institutionId
    ]) ?>

</div>

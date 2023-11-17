<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderType $model */

$this->title = '新建工单类型';
$this->params['breadcrumbs'][] = ['label' => '工单类型列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-order-type-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

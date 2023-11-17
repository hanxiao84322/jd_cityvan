<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderType $model */

$this->title = '修改工单类型';
$this->params['breadcrumbs'][] = ['label' => '工单类型列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="work-order-type-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

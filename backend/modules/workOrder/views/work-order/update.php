<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */

$this->title = '修改工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改工单';
?>
<div class="work-order-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

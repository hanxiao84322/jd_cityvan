<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */
/** @var string $orderNo */
/** @var string $logisticCompany */


$this->title = '新增工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-order-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

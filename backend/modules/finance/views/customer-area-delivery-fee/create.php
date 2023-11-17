<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CustomerAreaDeliveryFee $model */
/** @var int $institutionId */

$this->title = '新增客户区域运费';
$this->params['breadcrumbs'][] = ['label' => '客户区域运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-area-delivery-fee-create">

    <?= $this->render('_form', [
        'model' => $model,
        'institutionId' => $institutionId,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CustomerAreaDeliveryFee $model */
/** @var int $institutionId */

$this->title = '修改客户区域运费';
$this->params['breadcrumbs'][] = ['label' => '客户区域运费管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="customer-area-delivery-fee-update">

    <?= $this->render('_form', [
        'model' => $model,
        'institutionId' => $institutionId,
    ]) ?>

</div>

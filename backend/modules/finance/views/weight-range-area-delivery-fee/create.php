<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\WeightRangeAreaDeliveryFee $model */
/** @var int $institutionId */

$this->title = '新增区间运费';
$this->params['breadcrumbs'][] = ['label' => '区间运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="weight-range-area-delivery-fee-create">

    <?= $this->render('_form', [
        'model' => $model,
        'institutionId' => $institutionId,
    ]) ?>

</div>

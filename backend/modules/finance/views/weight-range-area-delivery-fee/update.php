<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\WeightRangeAreaDeliveryFee $model */
/** @var int $institutionId */

$this->title = '修改区间运费';
$this->params['breadcrumbs'][] = ['label' => '区间运费管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="weight-range-area-delivery-fee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'institutionId' => $institutionId,
    ]) ?>

</div>

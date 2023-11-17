<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticAreaDeliveryFee $model */

$this->title = '新增快递公司区域运费';
$this->params['breadcrumbs'][] = ['label' => '快递公司区域运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-area-delivery-fee-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

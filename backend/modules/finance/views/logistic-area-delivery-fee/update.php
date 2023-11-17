<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticAreaDeliveryFee $model */

$this->title = '修改快递公司区域运费';
$this->params['breadcrumbs'][] = ['label' => '快递公司区域运费管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-area-delivery-fee-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

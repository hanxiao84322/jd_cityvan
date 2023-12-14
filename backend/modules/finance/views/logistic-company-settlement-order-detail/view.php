<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDetail $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Settlement Order Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-settlement-order-detail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'settlement_order_no',
            'logistic_no',
            'warehouse_code',
            'logistic_id',
            'province',
            'city',
            'district',
            'weight',
            'size',
            'size_weight',
            'need_receipt_amount',
            'finish_time',
            'create_time',
        ],
    ]) ?>

</div>

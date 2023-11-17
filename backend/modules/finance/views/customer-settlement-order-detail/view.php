<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrderDetail $model */


$this->title = '结算明细';
$this->params['breadcrumbs'][] = ['label' => '结算明细', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="customer-settlement-order-detail-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'settlement_order_no',
            'logistic_no',
            [
                'attribute' => 'institution_id',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return \backend\models\Institution::getNameById($model->institution_id);
                    }
            ],
            [
                'attribute' => 'customer_id',
                'value' =>
                    function ($model) {
                        return \common\models\Customer::getNameById($model->customer_id);
                    },
            ],
            [
                'attribute' => 'province',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::getNameByCode($model->province);
                    },
            ],
            [
                'attribute' => 'city',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::getNameByCode($model->city);
                    },
            ],
            [
                'attribute' => 'district',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::getNameByCode($model->district);
                    },
            ],
            'weight',
            'need_receipt_amount',
            'create_time',
        ],
    ]) ?>

</div>

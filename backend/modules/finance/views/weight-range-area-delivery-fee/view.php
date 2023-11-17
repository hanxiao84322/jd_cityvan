<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\WeightRangeAreaDeliveryFee $model */

$this->title = '区间运费详情';
$this->params['breadcrumbs'][] = ['label' => '区间运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="weight-range-area-delivery-fee-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'institution_id',
                'value' =>
                    function ($model) {
                        return \backend\models\Institution::getNameById($model->institution_id);
                    },
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
            'city',
            'district',
            'first_weight_range_price',
            'sec_weight_range_price',
            'third_weight_range_price',
            'fourth_weight_range_price',
            'fourth_weight_range_price_float',
            'fifth_weight_range_price',
            'fifth_weight_range_price_float',
            'invoice_base_price',
            'face_order_fee',
            'return_fee',
            'return_base',
            'orders_base_fee',
            'under_orders_base_fee',
            'return_rate',
            'agent_rate',
            [
                'attribute' => 'is_cancel',
                'value' => function ($model) {
                    return \backend\models\CustomerAreaDeliveryFee::getIsCancelName($model->is_cancel);
                }
            ],
            'create_user',
            'create_time',
            'update_user',
            'update_time',
        ],
    ]) ?>

</div>

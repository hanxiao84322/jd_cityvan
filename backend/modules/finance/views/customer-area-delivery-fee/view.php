<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\CustomerAreaDeliveryFee;

/** @var yii\web\View $this */
/** @var backend\models\CustomerAreaDeliveryFee $model */


$this->title = '客户区域运费详情';
$this->params['breadcrumbs'][] = ['label' => '客户区域运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-area-delivery-fee-view">

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
            [
                'attribute' => 'fee_type',
                'value' =>
                    function ($model) {
                        return CustomerAreaDeliveryFee::getFeeName($model->fee_type);
                    },
            ],
            [
                'attribute' => 'fee_rules',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return CustomerAreaDeliveryFee::getFeeRules($model->fee_rules, $model->fee_type);
                    },
            ],
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

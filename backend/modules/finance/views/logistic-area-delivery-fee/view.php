<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticAreaDeliveryFee $model */

$this->title = '快递公司区域运费详情';
$this->params['breadcrumbs'][] = ['label' => '快递公司区域运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-area-delivery-fee-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'logistic_id',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
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
                        return \common\models\LogisticAreaDeliveryFee::getFeeName($model->fee_type);
                    },
            ],
            [
                'attribute' => 'fee_rules',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticAreaDeliveryFee::getFeeRules($model->fee_rules, $model->fee_type);
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
                    return \common\models\LogisticAreaDeliveryFee::getIsCancelName($model->is_cancel);
                }
            ],
            'create_user',
            'create_time',
            'update_user',
            'update_time',
        ],
    ]) ?>

</div>

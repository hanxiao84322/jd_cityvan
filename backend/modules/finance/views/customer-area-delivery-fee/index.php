<?php

use backend\models\CustomerAreaDeliveryFee;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\CustomerAreaDeliveryFeeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */
/* @var $cityList array */
/* @var $districtList array */


$this->title = '客户区域运费列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel, 'cityList' => $cityList, 'districtList' => $districtList]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'customer_name',
                    [
                        'attribute' => 'customer_type',
                        'value' =>
                            function ($model) {
                                return \common\models\Customer::getTypeName($model->customer_type);
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
                    //'invoice_base_price',
                    //'face_order_fee',
                    //'return_fee',
                    //'return_base',
                    //'orders_base_fee',
                    //'under_orders_base_fee',
                    //'return_rate',
                    //'agent_rate',
                    //'is_cancel',
                    'create_user',
                    'create_time',
                    //'update_user',
                    //'update_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                            },

                        ]
                    ],
                ],
            ]); ?>


        </div>
        <?= \common\widgets\LinkPager::widget([
            'pagination' => $pages,
            'firstPageLabel' => '首页',
            'lastPageLabel' => '末页',
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页',
            'go' => true,
            'totalCount' => isset($pages->totalCount) ? $pages->totalCount : 0
        ]);
        ?>
    </div>
</div>
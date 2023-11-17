<?php

use backend\models\WeightRangeAreaDeliveryFee;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\WeightRangeAreaDeliveryFeeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

/* @var $pages yii\data\ActiveDataProvider */
/* @var $cityList array */
/* @var $districtList array */


$this->title = '区间运费列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="weight-range-area-delivery-fee-index" >
    <div class="box">
        <div class="box-body" >
            <?php echo $this->render('_search', ['model' => $searchModel, 'cityList' => $cityList, 'districtList' => $districtList]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'options' => ['class' => 'hidden']//关闭分页
        ],
        'columns' => [
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
            'first_weight_range_price',
            'sec_weight_range_price',
            'third_weight_range_price',
            'fourth_weight_range_price',
            'fourth_weight_range_price_float',
            'fifth_weight_range_price',
            'fifth_weight_range_price_float',
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
            'update_user',
            'update_time',
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
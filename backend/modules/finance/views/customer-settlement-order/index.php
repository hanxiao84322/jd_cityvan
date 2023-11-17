<?php

use common\models\CustomerSettlementOrder;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */
/** @var int $institutionId */
/** @var int $level */

$this->title = '结算单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-settlement-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel,'institutionId' => $institutionId,'level' => $level]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1110px;white-space:nowrap;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'options' => ['class' => 'hidden']//关闭分页
        ],
        'columns' => [
            'settlement_order_no',
            'institution_name',
            'customer_name',
            'need_receipt_amount',
            'adjust_amount',
            'need_amount',
            'start_time',
            'end_time',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return CustomerSettlementOrder::getStatusName($model->status);
                    },
            ],
            'create_time',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {details}  {print} {collection}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                    },
                    'details' => function ($url, $model) {
                        return Html::a('结算明细', ['/finance/customer-settlement-order-detail/index', 'CustomerSettlementOrderDetailSearch[settlement_order_no]' => $model->settlement_order_no], ['target' => '_blank']);
                    },
                    'print' => function ($url, $model) {
                        return Html::a('打印', 'print?id=' . $model->id, ['target' => '_blank']);
                    },
                    'collection' => function ($url, $model) {
                        if ($model->status != CustomerSettlementOrder::STATUS_PAID) {
                            return Html::a('确认收款', 'collection?id=' . $model->id, ['target' => '_blank']);
                        } else {
                            return '';
                        }
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
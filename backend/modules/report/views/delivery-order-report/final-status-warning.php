<?php

use yii\grid\GridView;
use yii\helpers\Html;
use common\models\DeliveryOrder;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */


$this->title = '未达到最终状态预警报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('final-status-warning_search'); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:98%;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'logistic_no',
                    'warehouse_code',
                    'logistic_company_name',
                    'order_no',
                    'shipping_no',
                    'shipping_num',
                    'order_weight',
                    'order_weight_rep',
                    'shipping_weight',
                    'shipping_weight_rep',
                    'post_office_weight',
                    'receiver_name',
                    'receiver_phone',
                    'receiver_address',
                    'send_time',
                    'transporting_time',
                    'transported_time',
                    'delivering_time',
                    'delivered_time',
                    [
                        'header' => '状态',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align'=>'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getStatusName($model->status);
                        }
                    ],
                    'latest_track_info',
                    [
                        'header' => '是否延误',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align'=>'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getYesOrNotName($model->is_delay);
                        }
                    ],
                    [
                        'header' => '是否结算',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align'=>'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getYesOrNotName($model->is_agent_settle);
                        }
                    ],
                    'order_total_price',
                    'total_price',
                    'create_time',
                    'update_name',
                    'update_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('更新状态', 'update-status?id=' . $model->id, ['target' => '_blank']);
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

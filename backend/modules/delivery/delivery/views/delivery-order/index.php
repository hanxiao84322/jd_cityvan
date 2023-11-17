<?php

use common\models\DeliveryOrder;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
                    'send_time',
                    'logistic_no',
                    [
                        'header' => '当前状态',
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
                    'latest_track_time',
                    'warehouse_no',
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
                    'package_collection_time',
                    'transporting_time',
                    'transported_time',
                    'delivering_time',
                    'delivered_time',
                    'estimate_time',
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
                        'template' => '{view}  {update}  {create_work_order}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                            },
                            'create_work_order' => function ($url, $model) {
                                return Html::a('创建工单', '/index.php/workOrder/work-order/create?order_no=' . $model->order_no, ['target' => '_blank']);
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

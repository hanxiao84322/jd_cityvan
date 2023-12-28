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
                'options' => ['id' => 'myTable'],
                'columns' => [
                    'send_time',
                    'create_time',
                    'logistic_no',
                    [
                        'header' => '当前状态',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getStatusName($model->status);
                        }
                    ],
                    'latest_track_info',
                    'latest_track_time',
                    'warehouse_code',
                    'logistic_company_name',
                    'order_no',
                    'shipping_no',
                    'shipping_num',
                    [
                        'header' => '订单重量',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->order_weight;
                        }
                    ],
                    [
                        'header' => '订单重量(复查)',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->order_weight_rep;
                        }
                    ],
                    [
                        'header' => '包裹重量',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->shipping_weight;
                        }
                    ],
                    [
                        'header' => '包裹重量(复查)',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->shipping_weight_rep;
                        }
                    ],
                    'post_office_weight',
                    'receiver_name',
                    'receiver_phone',
                    'receiver_address',
                    'transporting_time',
                    'transported_time',
                    'delivering_time',
                    'delivered_time',
                    'replace_delivered_time',
                    'reject_time',
                    'reject_in_warehouse_time',
                    [
                        'header' => '是否与快递公司结算',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getYesOrNotName($model->is_logistic_company_settle);
                        }
                    ],
                    [
                        'header' => '支付快递公司金额',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->order_total_price;
                        }
                    ],
                    [
                        'header' => '收取京东金额',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getJdTotalPrice($model->warehouse_code, $model->total_price, $model->split_total_price);
                        }
                    ],
                    'update_name',
                    'update_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'position: sticky; right: -11px; background-color: #ffffff'],
                        'header' => '操作',
                        'template' => '{view}  {update} {view_work_order} {deal_work_order} {create_delivery_adjust_order}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                if (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) {
                                    return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                                }
                            },
                            'view_work_order' => function ($url, $model) {
                                if (\common\models\WorkOrder::findOne(['logistic_no' => $model->logistic_no])) {
                                    return Html::a('查看工单', '/workOrder/work-order/view?logistic_no=' . $model->logistic_no, ['target' => '_blank']);
                                }
                            },
                            'deal_work_order' => function ($url, $model) {
                                if (\common\models\WorkOrder::findOne(['logistic_no' => $model->logistic_no])) {
                                    if (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                        if (\common\models\WorkOrder::getStatus($model->logistic_no) == \common\models\WorkOrder::STATUS_FINISHED) {
                                            return Html::a('打开工单', '/workOrder/work-order/deal?logistic_no=' . $model->logistic_no, ['target' => '_blank']);
                                        } else {
                                            return Html::a('处理工单', '/workOrder/work-order/deal?logistic_no=' . $model->logistic_no, ['target' => '_blank']);
                                        }
                                    } else {
                                        return Html::a('处理工单', '/workOrder/work-order/deal?logistic_no=' . $model->logistic_no, ['target' => '_blank']);
                                    }
                                } else {
                                    if (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                        return Html::a('新建工单', '/workOrder/work-order/create?logistic_no=' . $model->logistic_no, ['target' => '_blank']);
                                    }
                                }

                            },
                            'create_delivery_adjust_order' => function ($url, $model) {
                                if (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                    return Html::a('新建调整单', '/delivery/delivery-adjust-order/create?logistic_no=' . $model->logistic_no, ['target' => '_blank']);
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
<script>
    <?php $this->beginBlock('js') ?>
    $(function () {
        const $table = $('#myTable');
        const $rows = $table.find('tbody tr');

        const scrollLeft = $(window).scrollLeft();

        $rows.each(function () {
            const $row = $(this);
            const $lastCell = $row.find('td:last-child');

            $lastCell.css({
                'position': 'sticky',
                'right': scrollLeft - 11,
                'background-color': "#ffffff"
            });
        });
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

<?php

use common\models\DeliveryOrder;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '未结算订单预警报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('wait-settlement-warning_search', ['model' => $searchModel]); ?>
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
                    'send_time',
                    'create_time',
                    [
                        'header' => '未结算天数',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->days;
                        }
                    ],
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
                    'latest_track_time',
                    'shipping_num',
                    'order_weight',
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
                    [
                        'header' => '邮局重量',
                        'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->post_office_weight;
                        }
                    ],
                    'receiver_name',
                    'receiver_phone',
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
                            return $model->total_price;
                        }
                    ],
                    'update_name',
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
                                if (\Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_SYSTEM) {
                                    return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
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

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '查看工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="work-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'logistic_no',
            'work_order_no',
            'order_no',
            'warehouse_code',
            'jd_work_order_no',
            [
                'label' => '物流重量',
                'value' =>
                    function ($model) {
                        return \common\models\DeliveryOrder::getPostOfficeWeight($model->logistic_no) . '公斤';
                    },
            ],
            'assign_username',
            'operate_username',
            [
                'attribute' => 'logistic_id',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            [
                'attribute' => 'type',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrderType::getTypeName($model->type);
                    },
            ],
            [
                'attribute' => 'priority',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getPriorityName($model->priority);
                    },
            ],

            'receive_name',
            'receive_phone',
            'receive_address',
            'order_create_num',
            [
                'attribute' => 'system_create',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getCreateName($model->system_create);
                    },
            ],
            [
                'attribute' => 'ordinary_create',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getCreateName($model->ordinary_create);
                    },
            ],
            [
                'attribute' => 'jd_create',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getCreateName($model->jd_create);
                    },
            ],
            'penalty_amount',
            'description',
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getStatusName($model->status);
                    },
            ],
            'create_time',
            'create_username',
            'update_time',
            'update_username',
            'finished_time',
        ],
    ]) ?>

    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $orderFilesSearchDataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'name',
                    'create_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'position: sticky; right: -11px; background-color: #ffffff'],
                        'header' => '操作',
                        'template' => '{download} {delete}',
                        'buttons' => [
                            'download' => function ($url, $model) {
                                return Html::a('下载', 'download-order-file?id=' . $model->id, ['target' => '_blank']);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('删除', 'delete-order-file?id=' . $model->id, [
                                    'data' => [
                                        'confirm' => '确定要删除该附件吗?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ]
                    ],
                ],
            ]); ?>
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
                    'reply_time',
                    'reply_name',
                    [
                        'header' => '回复内容',
                        'headerOptions' => [
                            'style' => 'text-align:left;',
                            'width' => '520px'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'left']],

                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->reply_content;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($workOrderReplyModel) {
                                return \common\models\WorkOrder::getStatusName($workOrderReplyModel->status);
                            },
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>


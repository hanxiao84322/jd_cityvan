<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryAdjustOrder $model */

$this->title = '查看订单调整单';
$this->params['breadcrumbs'][] = ['label' => '订单调整单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="delivery-adjust-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'logistic_no',
            'adjust_order_no',
            'adjust_amount',
            [
                'attribute' => 'type',
                'value' =>
                    function ($model) {
                        return \common\models\DeliveryAdjustOrder::getTypeName($model->type);
                    },
            ],
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\DeliveryAdjustOrder::getStatusName($model->status);
                    },
            ],
            'note',
            'create_time',
            'create_name',
        ],
    ]) ?>

</div>
<div class="box">
    <div class="box-body">
        <?= \yii\grid\GridView::widget([
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

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrderDetailSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $settlement_order_no */
/* @var $pages yii\data\ActiveDataProvider */


$this->title = '结算明细列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-settlement-order-detail-index">
    <div class="box">
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'action' => ['/finance/customer-settlement-order-detail/export'],
                'method' => 'get',
                'id' => 'search_form',
            ]); ?>
            <div class="form-group">
                <?= Html::hiddenInput("CustomerSettlementOrderDetailSearch[settlement_order_no]", $settlement_order_no)?>
            <?= Html::submitButton('导出', ['class' => 'btn btn-info', 'style' => 'margin-left:15px']) ?>
            </div>
            <?php ActiveForm::end(); ?>
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
            'settlement_order_no',
            'logistic_no',
            [
                'header' => '状态',
                'format' => 'raw',
                'value' => function ($model) {
                    return \common\models\DeliveryOrder::getStatusName($model->order_status);
                }
            ],
            'finish_time',
            'sender_name',
            'sender_phone',
            'sender_company',
            'sender_address',
            'weight',
            'size',
            'size_weight',
            'need_receipt_amount',
            'create_time',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} ',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('查看', '/finance/customer-settlement-order-detail/view?id=' . $model->id, ['target' => '_blank']);
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
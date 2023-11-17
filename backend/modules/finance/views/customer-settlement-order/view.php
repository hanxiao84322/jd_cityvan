<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrder $model */
/* @var $pages yii\data\ActiveDataProvider */
/** @var string $settlement_order_no */

$this->title = '结算单详情';
$this->params['breadcrumbs'][] = ['label' => '结算单列表', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="customer-settlement-order-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'settlement_order_no',
            [
                'attribute' => 'institution_id',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return \backend\models\Institution::getNameById($model->institution_id);
                    }
            ],
            [
                'attribute' => 'customer_id',
                'value' =>
                    function ($model) {
                        return \common\models\Customer::getNameById($model->customer_id);
                    },
            ],
            'need_receipt_amount',
            'need_pay_amount',
            'adjust_amount',
            'need_amount',
            'start_time',
            'end_time',
            [
                'label' => '状态',
                'value' =>
                    function ($model) {
                        return \common\models\CustomerSettlementOrder::getStatusName($model->status);
                    },
            ],
            [
                'attribute' => 'pay_image_path',
                'format' => 'raw',
                'visible' => !empty($model->pay_image_path),
                'value' =>
                    function ($model) {
                        return Html::a(Html::img($model->pay_image_path, ["width"=>"80","height"=>"80"]), '#', [
                            'data-toggle' => 'modal',
                            'data-target' => '#pay_image_path'
                        ]);
                    },
            ],
            'create_name',
            'create_time',
            'update_name',
            'update_time',
        ],
    ]) ?>

</div>
<div class="box">
    <div class="box-body">
        结算明细
    </div>
</div>
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
<?php

Modal::begin([
    'id' => 'pay_image_path',
    'header' => '<h5>支付凭证</h5>',
]);
?>
<?php echo Html::img($model->pay_image_path); ?>
<?php
Modal::end();

?>
<!--startprint-->
<style>
    .product-div {display: flex;}
    .product-div-left {float: left; width: 50px;}
    .product-div-right {float: right; width: 275px;}
    .invoice-div-right {float: right; width: 225px;}
</style>
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerSettlementOrder $model */

$this->title = '打印结算单';
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
            ],
        ]); ?>


    </div>
</div>
<!--endprint-->
<script type="text/javascript">
    window.onload = function doPrint() {
        bdhtml=window.document.body.innerHTML;
        sprnstr="<!--startprint-->";
        eprnstr="<!--endprint-->";
        prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr));
        prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));
        window.document.body.innerHTML=prnhtml;
        window.document.body.style.margin="0px";
        window.document.body.style.height="90%";
        window.print();
    }
</script>

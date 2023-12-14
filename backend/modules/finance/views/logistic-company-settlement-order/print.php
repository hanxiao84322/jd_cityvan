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

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrder $model */

$this->title = '查看结算单详情';
$this->params['breadcrumbs'][] = ['label' => '结算单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-settlement-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'settlement_order_no',
            'logistic_company_check_bill_no',
            [
                'label' => '快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            'warehouse_code',
            [
                'label' => '类型',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanySettlementOrder::getTypeName($model->type);
                    },
            ],
            'date',
            'order_num',
            'need_amount',
            'need_pay_amount',
            'adjust_amount',
            'preferential_amount',
            [
                'label' => '差异调整方案',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanySettlementOrder::getDiffAdjustPlanListName($model->diff_adjust_plan);
                    },
            ],
            [
                'label' => '手动输入金额',
                'visible' => ($model->diff_adjust_plan == 2) ? 1 : 0,
                'value' =>
                    function ($model) {
                        return $model->input_amount;
                    },
            ],
            [
                'label' => '调整项',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanySettlementOrder::getAdjustTerm($model->adjust_term);
                    },
            ],
            [
                'label' => '状态',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanySettlementOrder::getStatusName($model->status);
                    },
            ],
            'create_name',
            'create_time',
            'update_name',
            'update_time',
        ],
    ]) ?>
    <p><h2>对账单信息</h2></p>
    <?= DetailView::widget([
        'model' => $logisticCompanyCheckBillModel,
        'attributes' => [
            'logistic_company_check_bill_no',
            [
                'attribute' => 'logistic_id',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            'warehouse_code',
            'date',
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanyCheckBill::getStatusName($model->status);
                    },
            ],
            'logistic_company_order_num',
            'system_order_num',
            [
                'label' => '差异单量',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return "<b style='color: red;'>" . $model->logistic_company_order_num - $model->system_order_num;
                    },
            ],
            'logistic_company_order_price',
            'system_order_price',
            [
                'label' => '差异金额',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return "<b style='color: red;'>" . $model->logistic_company_order_price - $model->system_order_price . "</b>";
                    },
            ],
            'create_username',
            'create_time',
            'update_username',
            'update_time',
            'note:ntext',
        ],
    ]) ?>
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
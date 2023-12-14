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
/** @var common\models\LogisticCompanyCheckBill $model */

$this->title = '查看对账单详情';
$this->params['breadcrumbs'][] = ['label' => '对账列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-check-bill-view">


    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
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
                        return "<b style='color: red;'>" . $model->logistic_company_order_num - $model->system_order_num . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . Html::a('下载差异单', ['/finance/logistic-company-check-bill-detail/export-data', 'LogisticCompanyCheckBillDetailSearch[logistic_company_check_bill_no]' => $model->logistic_company_check_bill_no, 'is_diff' => 1], ['target' => '_blank']);;
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

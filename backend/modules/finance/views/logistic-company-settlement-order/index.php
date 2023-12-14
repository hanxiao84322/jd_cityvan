<?php

use common\models\LogisticCompanySettlementOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '结算单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-settlement-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1110px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'settlement_order_no',
                    'logistic_company_check_bill_no',
                    'logistic_company_name',
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
                        'label' => '状态',
                        'value' =>
                            function ($model) {
                                return \common\models\LogisticCompanySettlementOrder::getStatusName($model->status);
                            },
                    ],
                    'create_name',
                    'create_time',
                    //'update_name',
                    //'update_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view} {confirm} {finish} {print} {delete} ',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'confirm' => function ($url, $model) {
                if ($model->status == LogisticCompanySettlementOrder::STATUS_WAIT) {
                    return Html::a('确认', 'confirm?id=' . $model->id);

                }
                            },
                            'finish' => function ($url, $model) {
                                if ($model->status == LogisticCompanySettlementOrder::STATUS_CONFIRM) {
                                    return Html::a('结算完成', 'finish?id=' . $model->id);

                                }
                            },
                            'print' => function ($url, $model) {
                                return Html::a('打印', 'print?id=' . $model->id, ['target' => '_blank']);
                            },
                            'delete' => function ($url, $model) {
                                if ($model->status == LogisticCompanySettlementOrder::STATUS_WAIT) {

                                    return Html::a('删除', ['delete', 'id' => $model->id], [
                                        'data' => [
                                            'confirm' => '确定删除吗?',
                                            'method' => 'post',
                                        ],
                                    ]);
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

<?php

use common\models\LogisticCompanyCheckBill;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '对账单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-check-bill-index">


    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1150px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'logistic_company_check_bill_no',
                    'logistic_company_name',
                    'warehouse_code',
                    [
                        'header' => '类型',
                        'value' => function ($model) {
                            return LogisticCompanyCheckBill::getTypeName($model->type);
                        }
                    ],
                    'date',
                    'logistic_company_order_num',
                    'system_order_num',
                    'system_order_price',
                    [
                        'header' => '状态',
                        'value' => function ($model) {
                            return LogisticCompanyCheckBill::getStatusName($model->status);
                        }
                    ],
                    'create_username',
                    'create_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view} {details}  {print} {delete} {create_settlement}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'details' => function ($url, $model) {
                                return Html::a('对账明细', ['/finance/logistic-company-check-bill-detail/index', 'LogisticCompanyCheckBillDetailSearch[logistic_company_check_bill_no]' => $model->logistic_company_check_bill_no], ['target' => '_blank']);
                            },
                            'print' => function ($url, $model) {
                                return Html::a('打印', 'print?id=' . $model->id, ['target' => '_blank']);
                            },
                            'delete' => function ($url, $model) {
                                if ($model->status == \common\models\LogisticCompanyCheckBill::STATUS_NEW) {
                                    return Html::a('删除', ['delete', 'id' => $model->id], [
                                        'data' => [
                                            'confirm' => '确定删除吗?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                }
                            },
                            'create_settlement' => function ($url, $model) {
                                if ($model->status == \common\models\LogisticCompanyCheckBill::STATUS_CONFIRMED) {

                                    return Html::a('生成结算单', '/finance/logistic-company-settlement-order/create?id=' . $model->id, ['target' => '_blank']);
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

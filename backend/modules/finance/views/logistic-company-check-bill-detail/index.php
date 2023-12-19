<?php

use common\models\LogisticCompanyCheckBillDetail;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillDetailSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '对账单明细列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-check-bill-detail-index">


    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1110px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'logistic_company_check_bill_no',
                    'warehouse_code',
                    'logistic_id',
                    'logistic_no',
                    'weight',
                    'price',
                    'system_weight',
                    'system_price',
                    [
                        'header' => '状态',
                        'value' => function ($model) {
                            return LogisticCompanyCheckBillDetail::getStatusName($model->status);
                        }
                    ],
                    'note:ntext',
                    'create_username',
                    'create_time',
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


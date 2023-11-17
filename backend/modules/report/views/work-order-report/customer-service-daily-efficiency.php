<?php

use common\models\CustomerServiceDailyEfficiency;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiencySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '客服每日工作效率报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-service-daily-efficiency-index">
    <div class="box">
        <div class="box-body">
            <?php echo $this->render('customer-service-daily-efficiency_search', ['model' => $searchModel]); ?>
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
                    'date',
                    'username',
                    'name',
                    'type',
                    'work_order_create_num',
                    'work_order_deal_num',
                    'work_order_finished_num',
                    'work_order_not_finished_num',
                    [
                        'attribute' => 'work_order_finished_rate',
                        'value' =>
                            function ($model) {
                                return $model->work_order_finished_rate * 100 . '%';
                            },
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


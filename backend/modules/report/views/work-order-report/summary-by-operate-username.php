<?php

use common\models\WorkOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '工单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('summary-by-operate-username_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'warehouse_code',
                    [
                        'label' => '系统客服',
                        'value' =>
                            function ($model) {
                                return $model->create_username;
                            },
                    ],
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($model) {
                                return \common\models\WorkOrder::getStatusName($model->status);
                            },
                    ],
                    [
                        'label' => '工单数量',
                        'value' =>
                            function ($model) {
                                return $model->total_num;
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

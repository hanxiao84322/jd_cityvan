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
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
            'work_order_no',
            'order_no',
            [
                'attribute' => 'type',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getTypeName($model->type);
                    },
            ],
            [
                'attribute' => 'priority',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getPriorityName($model->priority);
                    },
            ],
            'receive_name',
            'receive_phone',
            'receive_address',
            'operate_username',
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getStatusName($model->status);
                    },
            ],
            'create_time',
            'create_username',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view}  {update}  {deal}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                    },
                    'deal' => function ($url, $model) {
                        if ($model->operate_username == \Yii::$app->user->getIdentity()['username'] && $model->status == WorkOrder::STATUS_WAIT_DEAL) {
                            return Html::a('处理', 'deal?id=' . $model->id, ['target' => '_blank']);
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

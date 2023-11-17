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
        <div class="box-body" style="overflow-x:scroll;width:98%;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'options' => ['id' => 'myTable1'],
                'columns' => [
                    'logistic_no',
                    'work_order_no',
                    'order_no',
                    'jd_work_order_no',
                    'assign_username',
                    'assign_name',
                    'operate_username',
                    'warehouse_code',
                    'logistic_company_name',
                    'work_order_type_name',
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
                    'order_create_num',
                    [
                        'attribute' => 'system_create',
                        'value' =>
                            function ($model) {
                                return \common\models\WorkOrder::getCreateName($model->system_create);
                            },
                    ],
                    [
                        'attribute' => 'ordinary_create',
                        'value' =>
                            function ($model) {
                                return \common\models\WorkOrder::getCreateName($model->ordinary_create);
                            },
                    ],
                    [
                        'attribute' => 'jd_create',
                        'value' =>
                            function ($model) {
                                return \common\models\WorkOrder::getCreateName($model->jd_create);
                            },
                    ],
                    'penalty_amount',
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($model) {
                                return \common\models\WorkOrder::getStatusName($model->status);
                            },
                    ],
                    'latest_reply',
                    'create_time',
                    'create_username',
                    'finished_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'position: sticky; right: -11px; background-color: #ffffff'],
                        'header' => '操作',
                        'template' => '{view}  {update}  {deal}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                if ($model->create_username == \Yii::$app->user->getIdentity()['username']) {
                                    return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                                }
                            },
                            'deal' => function ($url, $model) {
                                return Html::a('处理', 'deal?id=' . $model->id, ['target' => '_blank']);
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
<script>
    <?php $this->beginBlock('js') ?>
    $(function () {
        const $table = $('#myTable1');
        const $rows = $table.find('tbody tr');

        const scrollLeft = $(window).scrollLeft();

        $rows.each(function () {
            const $row = $(this);
            const $lastCell = $row.find('td:last-child');
            $lastCell.css({
                'position': 'sticky',
                'right': scrollLeft - 11,
                'background-color': "#ffffff"
            });
        });
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

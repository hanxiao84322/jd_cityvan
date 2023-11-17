<?php

use common\models\ImportantCustomer;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\ImportantCustomerSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '重点客户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="important-customer-index">
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
            'name',
            'phone',
            'address',
            'work_order_num',
            [
                'attribute' => 'level',
                'value' =>
                    function ($model) {
                        return \common\models\ImportantCustomer::getLevelName($model->level);
                    },
            ],
            'create_time',
            'create_name',
            //'update_time',
            //'update_name',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
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


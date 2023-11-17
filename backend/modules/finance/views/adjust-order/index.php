<?php

use common\models\AdjustOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\AdjustOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

/** @var int $institutionId */
/** @var int $level */

$this->title = '调整单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="adjust-order-index">


    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel, 'institutionId' => $institutionId, 'level' => $level]); ?>
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
                    'customer_name',
                    'settlement_no',
                    'adjust_amount',
                    [
                        'attribute' => 'type',
                        'value' =>
                            function ($model) {
                                return AdjustOrder::getTypeName($model->type);
                            },
                    ],
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($model) {
                                return AdjustOrder::getStatusName($model->status);
                            },
                    ],
                    //'note',
                    'create_time',
                    'create_name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update}',
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
<?php

use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */


$this->title = '运输预警报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('transport-warning_search'); ?>
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
                    [
                        'header' => '总数量',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->total_count;
                        }
                    ],
                    [
                        'header' => '正常数量',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->total_count - $model->transport_be_time_out - $model->transport_time_out - $model->transport_not_found - $model->delivering_time_out - $model->delivering_not_found;
                        }
                    ],
                        [
                            'header' => '运输即将超时',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return \yii\helpers\Html::a($model->transport_be_time_out, 'transport-warning-items?type=1', ['target' => '_blank']);
                            }
                        ],
                    [
                        'header' => '超时运输结束',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->transport_time_out, 'transport-warning-items?type=2', ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '无运输结束',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->transport_not_found, 'transport-warning-items?type=3', ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '超时配送中',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->delivering_time_out, 'transport-warning-items?type=4', ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '无配送中',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->delivering_not_found, 'transport-warning-items?type=5', ['target' => '_blank']);
                        }
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

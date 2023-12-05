<?php

use common\models\DeliveryOrderOverdueWarning;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderOverdueWarningSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '超期预警报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-warning-index">
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
            'date',
            'warehouse_code',
            'logistic_company_name',
            [
                'header' => '超期1天内',
                'format' => 'raw',
                'value' => function ($model) use ($create_time_end, $create_time_start) {
                    return \yii\helpers\Html::a($model->less_one_day, 'items?type=1&create_time_start=' . $create_time_start . '&create_time_end=' . $create_time_end . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                }
            ],
            [
                'header' => '超期1-2天',
                'format' => 'raw',
                'value' => function ($model) use ($create_time_end, $create_time_start) {
                    return \yii\helpers\Html::a($model->one_to_two_day, 'items?type=2&create_time_start=' . $create_time_start . '&create_time_end=' . $create_time_end . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                }
            ],
            [
                'header' => '超期2-3天',
                'format' => 'raw',
                'value' => function ($model) use ($create_time_end, $create_time_start) {
                    return \yii\helpers\Html::a($model->two_to_three_day, 'items?type=3&create_time_start=' . $create_time_start . '&create_time_end=' . $create_time_end . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                }
            ],
            [
                'header' => '超期3-5天',
                'format' => 'raw',
                'value' => function ($model) use ($create_time_end, $create_time_start) {
                    return \yii\helpers\Html::a($model->three_to_five_day, 'items?type=4&create_time_start=' . $create_time_start . '&create_time_end=' . $create_time_end . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                }
            ],
            [
                'header' => '超期5-7天',
                'format' => 'raw',
                'value' => function ($model) use ($create_time_end, $create_time_start) {
                    return \yii\helpers\Html::a($model->five_to_seven_day, 'items?type=5&create_time_start=' . $create_time_start . '&create_time_end=' . $create_time_end . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                }
            ],
            [
                'header' => '7天以上严重超期',
                'format' => 'raw',
                'value' => function ($model) use ($create_time_end, $create_time_start) {
                    return \yii\helpers\Html::a($model->more_seven_day, 'items?type=6&create_time_start=' . $create_time_start . '&create_time_end=' . $create_time_end . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
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

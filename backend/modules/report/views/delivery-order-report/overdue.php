<?php

use common\models\DeliveryOrder;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '超期预警报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('overdue_search', ['model' => $searchModel]); ?>
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
                    'warehouse_code',
                    'logistic_company_name',
                    [
                        'header' => '超期2天以内',
                        'format' => 'raw',
                        'value' => function ($model) use ($create_month) {
                            return \yii\helpers\Html::a($model->retention_two_days, 'overdue-items?type=1&create_month=' . $create_month . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '滞留2-3天',
                        'format' => 'raw',
                        'value' => function ($model) use ($create_month) {
                            return \yii\helpers\Html::a($model->retention_three_days, 'overdue-items?type=2&create_month=' . $create_month . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '滞留3-5天',
                        'format' => 'raw',
                        'value' => function ($model) use ($create_month) {
                            return \yii\helpers\Html::a($model->retention_five_days, 'overdue-items?type=3&create_month=' . $create_month . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '滞留5-7天',
                        'format' => 'raw',
                        'value' => function ($model) use ($create_month) {
                            return \yii\helpers\Html::a($model->retention_seven_days, 'overdue-items?type=4&create_month=' . $create_month . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '滞留7-10天',
                        'format' => 'raw',
                        'value' => function ($model) use ($create_month) {
                            return \yii\helpers\Html::a($model->retention_ten_days, 'overdue-items?type=5&create_month=' . $create_month . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '滞留10天以上',
                        'format' => 'raw',
                        'value' => function ($model) use ($create_month) {
                            return \yii\helpers\Html::a($model->retention_more_ten_days, 'overdue-items?type=6&create_month=' . $create_month . '&warehouse_code=' . $model->warehouse_code . '&logistic_id=' . $model->logistic_id, ['target' => '_blank']);
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

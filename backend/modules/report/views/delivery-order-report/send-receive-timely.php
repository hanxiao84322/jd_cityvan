<?php

use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '及时发货运输报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search'); ?>
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
                    'warehouse_code',
                    'logistic_company_name',
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
                            return $model->total_count - $model->receive_no_count - $model->transporting_no_count - $model->receive_timeout_count - $model->transporting_timeout_count;
                        }
                    ],
                    [
                        'header' => '无运输',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->transporting_no_count, 'send-receive-timely-items?type=2', ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '超时运输',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->transporting_timeout_count, 'send-receive-timely-items?type=4', ['target' => '_blank']);
                        }
                    ],
                ],
            ]); ?>


        </div>
    </div>
</div>

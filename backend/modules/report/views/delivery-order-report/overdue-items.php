<?php

use yii\grid\GridView;
use common\models\DeliveryOrder;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */
/** @var string $typeName */


$this->title = '超期预警报表明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'action' => ['/report/delivery-order-report/overdue-items-export'],
                'method' => 'get',
            ]); ?>

            <h4><?= $typeName;?></h4>
            <input type="hidden" name="type" value="<?= $type;?>">

            <?php ActiveForm::end(); ?>

        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:98%;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'send_time',
                    'create_time',
                    'logistic_no',
                    [
                        'header' => '当前状态',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrder::getStatusName($model->status);
                        }
                    ],
                    'latest_track_info',
                    'latest_track_time',
                    'warehouse_code',
                    'logistic_company_name',
                    'order_no',
                    'shipping_no',
                    'shipping_num',
                    'order_weight',
                    'order_weight_rep',
                    'shipping_weight',
                    'shipping_weight_rep',
                    'post_office_weight',
                    'receiver_name',
                    'receiver_phone',
                    'receiver_address',
                    'transporting_time',
                    'transported_time',
                    'delivering_time',
                    'delivered_time',
                    'replace_delivered_time',
                    'reject_time',
                    'reject_in_warehouse_time',
//                    'order_total_price',
//                    'total_price',
                    'update_name',
                    'update_time',
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

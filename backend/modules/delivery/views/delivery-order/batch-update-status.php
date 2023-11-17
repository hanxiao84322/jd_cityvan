<?php

use common\models\DeliveryOrder;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '批量更新订单状态';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">
    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_batch_update_status_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <?php if (!empty($dataProvider)) { ?>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-3">
                    <?= Html::dropDownList('status', '', \common\models\DeliveryOrder::$statusList, ['class' => 'form-control', 'id' => 'update_status']) ?>
                </div>
                <div class="col-xs-3">
                    <a class="btn btn-success" href="javascript:;" onclick="BatchPublish();">批量更新</a>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['id' => 'product_form'],
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [[
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'id',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['value' => $model->id];
                    }
                ],
                    'send_time',
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
                    'create_time',
                    'update_name',
                    'update_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update}  {create_work_order}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                            },
                            'create_work_order' => function ($url, $model) {
                                return Html::a('创建工单', '/index.php/workOrder/work-order/create?order_no=' . $model->order_no, ['target' => '_blank']);
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
    function BatchPublish() {
        var selected = $('#product_form tbody input:checked');
        if (selected.length == 0) {
            alert('请先选中需要批量操作的数据');
            return false;
        }
        var ids = '';
        var checkStatus = $('#update_status').val();
        selected.each(function () {
            var this_val = $(this).val();
            ids = ids + this_val + ',';
        });
        if (checkStatus == '') {
            alert('请选择需要调整的状态');
            return false;
        }
        $.post({
            url: '/delivery/delivery-order/ajax-batch-update-status',
            cache: false,
            type: "post",
            dataType: "JSON",
            data: {'ids': ids, 'status': checkStatus},
            success: function (result) {
                console.log(result);
                alert(result.msg);
                window.location.reload(true);
            }
        });
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

<?php

use common\models\DeliveryAdjustOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryAdjustOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '订单调整单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-adjust-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">

            <?= GridView::widget(['dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => ['logistic_no',
                    'adjust_order_no',
                    'adjust_amount',
                    [
                        'attribute' => 'type',
                        'value' =>
                            function ($model) {
                                return \common\models\DeliveryAdjustOrder::getTypeName($model->type);
                            },
                    ],
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($model) {
                                return \common\models\DeliveryAdjustOrder::getStatusName($model->status);
                            },
                    ],
                    //'note',
                    'create_time',
                    'create_name',
                    ['class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update}  {first_approve} {sec_approve}',
                        'buttons' => ['view' => function ($url, $model) {
                            return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                        },
                            'update' => function ($url, $model) {
                                if ($model->status != DeliveryAdjustOrder::STATUS_FIRST_APPROVED && $model->status != DeliveryAdjustOrder::STATUS_SEC_APPROVED && $model->status != DeliveryAdjustOrder::STATUS_FINISHED) {
                                    return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                                }
                            },
                            'first_approve' => function ($url, $model) {
                                if ($model->status == DeliveryAdjustOrder::STATUS_CREATE || $model->status == DeliveryAdjustOrder::STATUS_FIRST_REJECTED) {
                                    if (Yii::$app->user->can('/delivery/delivery-adjust-order/ajax-first-approve')) {
                                        return Html::a('一级审核', '#', [
                                            'data-toggle' => 'modal',
                                            'onclick' => 'showModal(' . $model->id . ')',
                                            'data-target' => '#first-approve-modal'    //此处对应Modal组件中设置的id
                                        ]);
                                    }
                                }
                            },
                            'sec_approve' => function ($url, $model) {
                                if ($model->status == DeliveryAdjustOrder::STATUS_FIRST_APPROVED || $model->status == DeliveryAdjustOrder::STATUS_SEC_REJECTED) {
                                    if (Yii::$app->user->can('/delivery/delivery-adjust-order/ajax-sec-approve')) {
                                        return Html::a('二级审核', '#', [
                                            'data-toggle' => 'modal',
                                            'onclick' => 'showModal(' . $model->id . ')',
                                            'data-target' => '#sec-approve-modal'    //此处对应Modal组件中设置的id
                                        ]);
                                    }

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

<?php

\yii\bootstrap\Modal::begin([
    'id' => 'first-approve-modal',
    'header' => '<h5>一级审核</h5>',
]);
?>
<p>审核备注</p>
<p>
    <?= Html::textarea('opinion', '', ['id' => 'opinion', 'style' => 'width: 500px; height:200px;']) ?>
    <?= Html::hiddenInput('delivery_adjust_id', '', ['id' => 'delivery_adjust_id']) ?>
</p>
</p>
<p><?= Html::button('通过', ['class' => 'btn btn-primary', 'id' => 'first_approve']) ?>
    &nbsp;<?= Html::button('驳回', ['class' => 'btn btn-outline-secondary', 'id' => 'first_reject']) ?>&nbsp;</p>

<p style="margin-top: 20px" id="message">
</p>
<?php
\yii\bootstrap\Modal::end();

?>

<?php

\yii\bootstrap\Modal::begin([
    'id' => 'sec-approve-modal',
    'header' => '<h5>二级审核</h5>',
]);
?>
<p>审核备注</p>
<p>
    <?= Html::textarea('sec_opinion', '', ['id' => 'sec_opinion', 'style' => 'width: 500px; height:200px;']) ?>
    <?= Html::hiddenInput('sec_delivery_adjust_id', '', ['id' => 'sec_delivery_adjust_id']) ?>
</p>
</p>
<p><?= Html::button('通过', ['class' => 'btn btn-primary', 'id' => 'sec_approve']) ?>
    &nbsp;<?= Html::button('驳回', ['class' => 'btn btn-outline-secondary', 'id' => 'sec_reject']) ?>&nbsp;</p>

<p style="margin-top: 20px" id="sec_message">
</p>
<?php
\yii\bootstrap\Modal::end();

?>

<script>
    <?php $this->beginBlock('js') ?>
    function showModal(id) {
        $('#delivery_adjust_id').val(id);
        $('#sec_delivery_adjust_id').val(id);
    }

    $(function () {
        $('#first_approve').click(function () {
            const btn = $('#first_approve');
            const delivery_adjust_id = $('#delivery_adjust_id').val();
            const opinion = $('#opinion').val();
            const show = $('#message');
            $.post({
                url: '/delivery/delivery-adjust-order/ajax-first-approve',
                cache: false,
                type: "post",
                dataType: "JSON",
                data: {'delivery_adjust_id': delivery_adjust_id, 'opinion': opinion, 'type': 'approve'},
                beforeSend: function () {
                    btn.html('<i class="fa fa-refresh fa-spin"></i> 审核中');
                    btn.attr('disabled', true);
                    show.html('');
                },
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.css('color', 'red');
                        show.html('审核失败，原因：' + result.errMsg);
                    } else {
                        alert('审核通过！');
                        location.reload(true);
                    }
                    btn.html('通过');
                    btn.attr('disabled', false);
                }
            });
        });
        $('#first_reject').click(function () {
            const btn = $('#first_reject');
            const delivery_adjust_id = $('#delivery_adjust_id').val();
            const opinion = $('#opinion').val();
            const show = $('#message');
            $.post({
                url: '/delivery/delivery-adjust-order/ajax-first-approve',
                cache: false,
                type: "post",
                dataType: "JSON",
                data: {delivery_adjust_id: delivery_adjust_id, opinion: opinion, type: 'reject'},
                beforeSend: function () {
                    btn.html('<i class="fa fa-refresh fa-spin"></i> 审核中');
                    btn.attr('disabled', true);
                    show.html('');
                },
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.css('color', 'red');
                        show.html('驳回失败，原因：' + result.errMsg);
                    } else {
                        alert('审核驳回！');
                        location.reload(true);
                    }
                    btn.html('驳回');
                    btn.attr('disabled', false);
                }
            });
        });
        $('#sec_approve').click(function () {
            const btn = $('#sec_approve');
            const delivery_adjust_id = $('#sec_delivery_adjust_id').val();
            const opinion = $('#sec_opinion').val();
            const show = $('#sec_message');
            $.post({
                url: '/delivery/delivery-adjust-order/ajax-sec-approve',
                cache: false,
                type: "post",
                dataType: "JSON",
                data: {delivery_adjust_id: delivery_adjust_id, opinion: opinion, type: 'approve'},
                beforeSend: function () {
                    btn.html('<i class="fa fa-refresh fa-spin"></i> 审核中');
                    btn.attr('disabled', true);
                    show.html('');
                },
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.css('color', 'red');
                        show.html('审核失败，原因：' + result.errMsg);
                    } else {
                        alert('审核通过！');
                        location.reload(true);
                    }
                    btn.html('通过');
                    btn.attr('disabled', false);
                }
            });
        });
        $('#sec_reject').click(function () {
            const btn = $('#sec_reject');
            const delivery_adjust_id = $('#sec_delivery_adjust_id').val();
            const opinion = $('#sec_opinion').val();
            const show = $('#sec_message');
            $.post({
                url: '/delivery/delivery-adjust-order/ajax-sec-approve',
                cache: false,
                type: "post",
                dataType: "JSON",
                data: {delivery_adjust_id: delivery_adjust_id, opinion: opinion, type: 'reject'},
                beforeSend: function () {
                    btn.html('<i class="fa fa-refresh fa-spin"></i> 审核中');
                    btn.attr('disabled', true);
                    show.html('');
                },
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.css('color', 'red');
                        show.html('驳回失败，原因：' + result.errMsg);
                    } else {
                        alert('审核驳回！');
                        location.reload(true);
                    }
                    btn.html('驳回');
                    btn.attr('disabled', false);
                }
            });
        });
        $('#close-btn').click(function () {
            location.reload(true);
        });
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

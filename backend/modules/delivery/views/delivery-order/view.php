<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrder $model */


$this->title = '运单详情';
$this->params['breadcrumbs'][] = ['label' => '运单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="delivery-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'send_time',
            'create_time',
            [
                'label' => '订单号',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return $model->logistic_no . '<input type="hidden" id="logistic_no" value="' . $model->logistic_no . '">&nbsp;&nbsp;&nbsp;' . Html::a('查看物流轨迹', '#', [
                                'class' => 'btn btn-success',
                                'data-toggle' => 'modal',
                                'id' => 'delivery-info-modal',
                                'data-target' => '#delivery-info'    //此处对应Modal组件中设置的id
                            ]);
                    },
            ],
            [
                'label' => '当前状态',
                'contentOptions' => ['style' => ['vertical-align'=>'middle']],
                'format' => 'raw',
                'value' => function ($model) {
                    return \common\models\DeliveryOrder::getStatusName($model->status);
                }
            ],
            'latest_track_info',
            'latest_track_time',
            'warehouse_code',
            'order_no',
            'shipping_no',
            'shipping_num',
            [
                'label' => '订单重量',
                'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->order_weight;
                }
            ],
            [
                'label' => '订单重量(复查)',
                'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->order_weight_rep;
                }
            ],
            [
                'label' => '包裹重量',
                'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->shipping_weight;
                }
            ],
            [
                'label' => '包裹重量(复查)',
                'visible' => (\Yii::$app->user->getIdentity()['type'] != \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) ? 1 : 0,
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->shipping_weight_rep;
                }
            ],
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
    ]) ?>

</div>
<?php

Modal::begin([
    'id' => 'delivery-info',
    'header' => '<h5>物流轨迹</h5>',
]);
?>
<div id="upload-result"
     style="height:220px;overflow:auto;overflow-x:hidden;border:1px solid #ccc;padding:5px">
</div>
<?php
Modal::end();

?>
<script>
    <?php $this->beginBlock('js') ?>
    $(function () {

        // 导入数据
        $('#delivery-info-modal').click(function () {

            var btn = $('#delivery-info-modal');
            var show = $('#upload-result');
            var logistic_no = $('#logistic_no').val();
            $.ajax({
                url: '/delivery/delivery-order/ajax-get-delivery-info-steps',
                async: true,
                type: 'get',
                dataType: 'json',
                data: {logistic_no: logistic_no},
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        show.html(result.msg);
                    } else {
                        show.html(result.html);
                    }
                }
            });
        });
    });


    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
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
            'order_no',
            'source',
            [
                'attribute' => 'customer_id',
                'value' =>
                    function ($model) {
                        return \common\models\Customer::getNameById($model->customer_id);
                    },
            ],
            [
                'attribute' => '组织机构',
                'value' =>
                    function ($model) {
                        return \backend\models\Institution::getNameById($model->institution_id);
                    },
            ],
            'delivery_no',
            [
                'label' => '快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            [
                'label' => '快递单号',
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
                'label' => '第二快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->sec_logistic_id);
                    },
            ],
            'sec_logistic_no',
            'destination_mark',
            'device_id',
            'device_receiver_name',
            'device_receiver_phone',
            'device_weight',
            'device_size',
            'device_volume',
            'send_time',
            'package_collection_time',
            'transporting_time',
            'transported_time',
            'delivering_time',
            'delivered_time',
            'estimate_time',
            'receiver_name',
            'receiver_company',
            'receiver_phone',
            'receiver_address',
            'sender_name',
            'sender_phone',
            'sender_company',
            'sender_address',
            'taker_name',
            'taker_code',
            'taker_company',
            'weight',
            'volume',
            'long',
            'wide',
            'high',
            'province',
            'city',
            'district',
            'towns',
            'village',
//            [
//                'label' => '面单照片',
//                'format' => 'raw',
//                'value' =>
//                    function ($model) {
//        return Html::img($model->logistic_image,["width"=>"84","height"=>"84"]);
//                    },
//            ],
            [
                'label' => '状态',
                'value' =>
                    function ($model) {
                        return \common\models\DeliveryOrder::getStatusName($model->status);
                    },
            ],
            [
                'label' => '是否上传面单',
                'value' => function ($model) {
                    return \common\models\DeliveryOrder::getYesOrNotName(\common\models\LogisticImage::find()->where(['logistic_no' => $model->logistic_no])->exists());
                }
            ],
            [
                'label' => '是否需要解析',
                'value' => function ($model) {
                    return \common\models\DeliveryOrder::getYesOrNotName($model->is_need_analysis_ocr);
                }
            ],
            'latest_track_info',
            [
                'label' => '是否延误',
                'value' =>
                    function ($model) {
                        return \common\models\DeliveryOrder::getYesOrNotName($model->is_delay);
                    },
            ],
            [
                'label' => '是否结算',
                'value' =>
                    function ($model) {
                        return \common\models\DeliveryOrder::getYesOrNotName($model->is_agent_settle);
                    },
            ],
            'truck_classes_no',
            'order_total_price',
            'total_price',
            'create_name',
            'create_time',
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
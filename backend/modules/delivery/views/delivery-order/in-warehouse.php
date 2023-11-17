<script src='http://localhost:8000/CLodopfuncs.js'></script>
<!--<script src='http://192.168.3.108:8000/CLodopfuncs.js'></script>-->
<style>
    .table-logistic_order {
        width: 73mm;
        height: 130mm;
        border: 1px solid black;
    }

    .table-logistic_order td {
        padding: 5px;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrder $model */
/** @var common\models\Warehouse $warehouseModel */
/** @var yii\widgets\ActiveForm $form */
/** @var string $barcodeSvg */


$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
$this->title = '更新拒收入库状态';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="delivery-image-form">

    <?php $form = ActiveForm::begin([
        'action' => ['in-warehouse'],
        'method' => 'post',
    ]); ?>
    <div class="row">
        <div class="form-group col-xs-4 field-customer-sender_name">
            <label class="control-label" for="customer-sender_name">快递单号</label>
            <input type="text" id="customer-sender_name" class="form-control input-sm" name="logistic_no" value=""
                   maxlength="100">
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php if (!empty($model)) { ?>
        <div class="delivery-order-view">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'send_time',
                    'create_time',
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
                        'label' => '当前状态',
                        'contentOptions' => ['style' => ['vertical-align' => 'middle']],
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
            ]) ?>

        </div>
    <?php } ?>
    <?php if (!empty($model)) { ?>
        <div class="row">
            <div class="form-group col-xs-4 field-customer-sender_name">
                <?= Html::checkbox('is_print', true, ['id' => 'is_print']) ?>&nbsp;&nbsp;打印<br>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('更新状态为拒收已入库', ['class' => 'btn btn-success', 'id' => 'update_status_submit']) ?>
        </div>
        <div class="row">
            <div class="form-group col-xs-4 field-customer-sender_name">
                <h3>面单样式</h3>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-4 field-customer-sender_name" id="logistic_order" style="width: 73mm;">
                <table class="table-logistic_order">
                    <tr>
                        <td align="center" valign="top" colspan="2" height="70"><?php echo $barcodeSvg; ?>
                            <br><?php echo $model->shipping_no; ?></td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" height="50" style="width: 165px;">发货仓：<?php echo $warehouseModel->name; ?></td>
                        <td align="left" valign="top" style="width: 165px;">电话：<?php echo $warehouseModel->contact_phone; ?></td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="40">地址：<?php echo $warehouseModel->address; ?></td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="10">
                            <hr size="1" color="black" width="90%">
                        </td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" height="50" style="width: 165px;">收件人：<?php echo $model->receiver_name; ?></td>
                        <td align="left" valign="top" style="width: 165px;">电话：<?php echo $model->receiver_phone; ?></td>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="40">地址：<?php echo $model->receiver_address; ?></td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="10">
                            <hr size="1" color="black" width="90%">
                        </td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="40">发货时间：<?php echo $model->send_time; ?></td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="40">入库时间：<?php echo date('Y-m-d H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" colspan="2" height="40%"></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php } ?>
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

        $('#update_status_submit').click(function () {
            var logistic_no = $('#logistic_no').val();
            var is_print = $('#is_print').val();
            $.ajax({
                url: '/delivery/delivery-order/ajax-in-warehouse',
                async: true,
                type: 'post',
                dataType: 'json',
                data: {logistic_no: logistic_no},
                success: function (result) {
                    console.log(result);
                    alert(result.msg);
                    if (is_print == true) {
                        window.scrollTo({top: 0, behavior: 'smooth'});

                        var strHTML = document.getElementById("logistic_order").innerHTML;
                        LODOP.SET_PRINT_PAGESIZE(1, 730, 1300, "京东");
                        LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
                        LODOP.SET_PRINT_MODE("PRINT_PAGE_PERCENT", "100%"); // 设置打印纸张缩放比例
                        LODOP.SET_PRINT_MODE("PRINT_MODE_SYNC", 2); // 同步打印模式，确保设置生效
                        LODOP.ADD_PRINT_HTM(0, 0, "100%", "90%", strHTML);
                        LODOP.PRINTA();
                    }
                }
            });
        });
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

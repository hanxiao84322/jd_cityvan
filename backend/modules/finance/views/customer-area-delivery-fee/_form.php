<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \backend\models\CustomerAreaDeliveryFee;

\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var backend\models\CustomerAreaDeliveryFee $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */

$options = [
    'options' => ['class' => 'form-group col-xs-4'],
    'inputOptions' => ['class' => 'form-control input-sm']
];
$rangeFeeData = [
    'first_weight_range_price' => '',
    'sec_weight_range_price' => '',
    'third_weight_range_price' => '',
    'fourth_weight_range_price' => '',
    'fourth_weight_range_price_float' => '',
    'fifth_weight_range_price' => '',
    'fifth_weight_range_price_float' => '',
];
$firstAndFollowFeeData = [
    'weight' => '',
    'price' => '',
    'follow_weight' => '',
    'follow_price' => '',

];
if (!$model->isNewRecord) {

    if ($model->fee_type == CustomerAreaDeliveryFee::FEE_TYPE_RANGE) {
        $rangeFeeData = json_decode($model->fee_rules, true);
    } else {
        $firstAndFollowFeeData = json_decode($model->fee_rules, true);
    }
}
?>

<div class="customer-area-delivery-fee-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if ($model->isNewRecord) { ?>
        <div class="row">
            <input type="hidden" id="institution_id" class="form-control input-sm"
                   name="CustomerAreaDeliveryFee[institution_id]" value="<?php echo $institutionId; ?>"
                   maxlength="10">
            <?= $form->field($model, 'customer_type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\Customer::$typeList, ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'customer_type'])->label('客户类型'); ?>
            <?= $form->field($model, 'customer_id', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'customer_id']); ?>
        </div>
    <?php } else { ?>
        <div class="row">
            <input type="hidden" id="customer_id" class="form-control input-sm"
                   name="CustomerAreaDeliveryFee[institution_id]" value="<?php echo $model->customer_id; ?>"
                   maxlength="10">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">客户</label>
                <input type="text" class="form-control" name=""
                       value="<?php echo \common\models\Customer::getNameById($model->customer_id); ?>"
                       aria-required="true"
                       aria-invalid="false" readonly>
            </div>
        </div>

    <?php } ?>
    <div class="row">
        <?= $form->field($model, 'province', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Cnarea::getAllByLevel(\common\models\Cnarea::LEVEL_ONE), 'area_code', 'name'), ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'province']); ?>
        <?= $form->field($model, 'city', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'city']); ?>
        <?= $form->field($model, 'district', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'district']); ?>
    </div>
    <div class="row">

        <?= $form->field($model, 'fee_type', ['options' => ['class' => 'col-xs-3']])->dropDownList(CustomerAreaDeliveryFee::$feeTypeList, ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'fee_type'])->label('运费类型'); ?>
    </div>


    <div id="first_and_follow"
         style="display: <?php if (!$model->isNewRecord && $model->fee_type != CustomerAreaDeliveryFee::FEE_TYPE_RANGE) { ?>block;<?php } else { ?> none; <?php } ?>">
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">首重(千克)</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[first_and_follow_fee_data][weight]"
                       value="<?= $firstAndFollowFeeData['weight'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">首重价格(元/千克)</label>
                <input type="text" class="form-control" name="CustomerAreaDeliveryFee[first_and_follow_fee_data][price]"
                       value="<?= $firstAndFollowFeeData['price'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">续重(千克)</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[first_and_follow_fee_data][follow_weight]"
                       value="<?= $firstAndFollowFeeData['follow_weight'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">续重价格(元/千克)</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[first_and_follow_fee_data][follow_price]"
                       value="<?= $firstAndFollowFeeData['follow_price'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
    </div>

    <div id="fee_range"
         style="display: <?php if (!$model->isNewRecord && $model->fee_type == CustomerAreaDeliveryFee::FEE_TYPE_RANGE) { ?>block;<?php } else { ?> none; <?php } ?>">
        <div class="row">

            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">一阶0-1千克</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][first_weight_range_price]"
                       value="<?= $rangeFeeData['first_weight_range_price'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">二阶1-2千克</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][sec_weight_range_price]"
                       value="<?= $rangeFeeData['sec_weight_range_price'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">三阶2-3千克</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][third_weight_range_price]"
                       value="<?= $rangeFeeData['third_weight_range_price'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">四阶3-10千克</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][fourth_weight_range_price]"
                       value="<?= $rangeFeeData['fourth_weight_range_price'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">四阶（浮动价）元/千克</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][fourth_weight_range_price_float]"
                       value="<?= $rangeFeeData['fourth_weight_range_price_float'] ?>"
                       aria-required="true"
                       aria-invalid="false">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label" for="CustomerAreaDeliveryFee-weight">五阶10千克以上</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][fifth_weight_range_price]"
                       value="<?= $rangeFeeData['fifth_weight_range_price'] ?>"
                >
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <label class="control-label">五阶（浮动价）元/千克</label>
                <input type="text" class="form-control"
                       name="CustomerAreaDeliveryFee[range_fee_data][fifth_weight_range_price_float]"
                       value="<?= $rangeFeeData['fifth_weight_range_price_float'] ?>"
                >
            </div>
        </div>
    </div>
    <div class="row">
        <?= $form->field($model, 'invoice_base_price', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'face_order_fee', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'return_fee', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'return_base', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'orders_base_fee', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'under_orders_base_fee', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'return_rate', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'agent_rate', ['options' => ['class' => 'col-xs-3']])->textInput() ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'is_cancel', ['options' => ['class' => 'col-xs-3']])->dropDownList(CustomerAreaDeliveryFee::$isCancelList); ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>

    $(function () {
        $('#fee_type').change(function () {
            var fee_type = $(this).val();
            if (fee_type == <?= CustomerAreaDeliveryFee::FEE_TYPE_FIRST_WEIGHT_AND_FOLLOW?>) {
                $('#first_and_follow').css('display', 'block');
                $('#fee_range').css('display', 'none');
            }
            if (fee_type == <?= CustomerAreaDeliveryFee::FEE_TYPE_RANGE?>) {
                $('#first_and_follow').css('display', 'none');
                $('#fee_range').css('display', 'block');
            }
        });

        $(".select2").select2({language: 'zh-CN'});

        $('#customer_type').change(function () {
            var customer_type = $(this).val();
            var institution_id = $("#institution_id").val();
            $.ajax({
                type: "post",
                url: "/customer/customer/ajax-get-list-by-type-and-institution-id",
                cache: false,
                data: {customer_type: customer_type, institution_id: institution_id},
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    if (result.status == 1) {
                        var html_default = '<option value="">-全部-</option>';
                        var html = html_default + result.data;
                        $("#customer_id").html(html);
                        $(".select2").select2({language: 'zh-CN'});
                    } else {
                        alert(result.errorMsg);
                    }
                }
            });

        });
        $('#province').change(function(){
            var area_code = $(this).val();
            $.ajax({
                type: "get",
                url: "/institution/cnarea/ajax-get-list",
                cache: false,
                data: {area_code: area_code},
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    if (result.status == 1) {
                        var html_default = '<option value="">-全部-</option>';
                        var html = html_default + result.data;
                        $("#city").html(html);
                        $("#district").html(html_default);
                        $(".select2").select2({language:'zh-CN'});
                    }else{
                        alert(result.errorMsg);
                    }
                }
            });

        });
        $('#city').change(function () {
            var area_code = $(this).val();
            $.ajax({
                type: "get",
                url: "/institution/cnarea/ajax-get-list",
                cache: false,
                data: {area_code: area_code},
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    if (result.status == 1) {
                        var html_default = '<option value="">-全部-</option>';
                        html = html_default + result.data;
                        $("#district").html(html);
                        $(".select2").select2({language: 'zh-CN'});
                    } else {
                        alert(result.errorMsg);
                    }
                }
            });

        });

    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var backend\models\WeightRangeAreaDeliveryFee $model */
/** @var yii\widgets\ActiveForm $form */
/** @var int $institutionId */

?>

<div class="weight-range-area-delivery-fee-form">

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
            <div class="col-xs-3 field-CustomerAreaDeliveryFee-weight required has-success">
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

        <?= $form->field($model, 'first_weight_range_price', ['options' => ['class' => 'col-xs-3']]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'sec_weight_range_price', ['options' => ['class' => 'col-xs-3']]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'third_weight_range_price', ['options' => ['class' => 'col-xs-3']]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'fourth_weight_range_price', ['options' => ['class' => 'col-xs-3']]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'fourth_weight_range_price_float', ['options' => ['class' => 'col-xs-3']]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'fifth_weight_range_price', ['options' => ['class' => 'col-xs-3']]) ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'fifth_weight_range_price_float', ['options' => ['class' => 'col-xs-3']]) ?>
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
        <?= $form->field($model, 'is_cancel', ['options' => ['class' => 'col-xs-3']])->dropDownList(\backend\models\CustomerAreaDeliveryFee::$isCancelList); ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>

    $(function () {
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

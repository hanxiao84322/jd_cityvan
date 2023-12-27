<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\backend\assets\Select2Asset::register($this);
/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRules $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="logistic-company-fee-rules-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?php if (!$model->isNewRecord) {?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanyFeeRules::$typeList, ['disabled' => 'disabled'])->label('类型'); ?>
        <?php } else {?>
        <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanyFeeRules::$typeList)->label('类型'); ?>
        <?php }?>
    </div>
    <div class="row">
        <?php if (!$model->isNewRecord) {?>
            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---','disabled' => 'disabled'])->label('快递公司'); ?>
        <?php } else {?>
            <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '---全选---'])->label('快递公司'); ?>

        <?php }?>
    </div>
    <div class="row">
        <?php if (!$model->isNewRecord) {?>
        <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'code', 'name'), ['prompt' => '---全选---','disabled' => 'disabled'])->label('仓库'); ?>
        <?php } else {?>
        <?= $form->field($model, 'warehouse_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Warehouse::getAll(), 'code', 'name'), ['prompt' => '---全选---'])->label('仓库'); ?>
        <?php }?>


    </div>
    <?php if (!$model->isNewRecord) {?>

    <div class="row">
        <?= $form->field($model, 'province_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Cnarea::getAllByLevel(\common\models\Cnarea::LEVEL_ONE), 'area_code', 'name'), ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'province','disabled' => 'disabled']); ?>
        <?= $form->field($model, 'city_code', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'city','disabled' => 'disabled']); ?>
        <?= $form->field($model, 'district_code', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'district','disabled' => 'disabled']); ?>
    </div>
    <?php } else {?>
        <div class="row">
            <?= $form->field($model, 'province_code', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Cnarea::getAllByLevel(\common\models\Cnarea::LEVEL_ONE), 'area_code', 'name'), ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'province']); ?>
            <?= $form->field($model, 'city_code', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'city']); ?>
            <?= $form->field($model, 'district_code', ['options' => ['class' => 'col-xs-3']])->dropDownList([], ['prompt' => '-全部-', 'class' => 'form-control select2', 'id' => 'district']); ?>
        </div>
    <?php }?>
</div>
<div class="row">
    <?= $form->field($model, 'weight', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true])->label('首重(公斤)') ?>
</div>
<div class="row">
    <?= $form->field($model, 'weight_round_rule', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanyFeeRules::$weightRoundRuleList)->label('续重取整规则') ?>
</div>
<div class="row" id="input_amount_input" style="display: <?php if ($model->type == \common\models\LogisticCompanyFeeRules::TYPE_WAREHOUSE) {?> block <?php } else {?>none<?php } ?>;">
    <?= $form->field($model, 'continue_count_rule', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanyFeeRules::$continueCountList)->label('续重计算规则') ?>
</div>

<div class="row">
    <?= $form->field($model, 'price', ['options' => ['class' => 'col-xs-3']])->textInput(['maxlength' => true])->label('首重价格(元)') ?>
</div>
<div class="row">
    <?= $form->field($model, 'continue_weight_rule', ['options' => ['class' => 'col-xs-3']])->textarea(['rows' => 6])->label('续重规则') ?>
    <br>
    <p>格式：一行为一阶<br>
        区间开始重量，区间结束重量，价格<br>
        区间结束重量不填则代表区间开始重量以上<br>
        举例：<br>
        1,2,3<br>
        2,3,2<br>
        3,,2</p>
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
        $('#province').change(function () {
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
        $('#logisticcompanyfeerules-type').change(function () {
            const diff_adjust_plan = $('#logisticcompanyfeerules-type').val();

            if (diff_adjust_plan == 2) {
                $('#input_amount_input').css('display', 'block');
            } else {
                $('#input_amount_input').css('display', 'none');
            }

        });
    });
    <?php if (!$model->isNewRecord) {?>
    $(document).ready(function () {
        $.ajax({
            type: "get",
            url: "/institution/cnarea/ajax-get-list",
            cache: false,
            data: {area_code: <?php echo $model->province_code;?>, select_code: <?php echo $model->city_code;?>},
            dataType: 'json',
            success: function (result) {
                console.log(result);
                if (result.status == 1) {
                    var html_default = '<option value="">-全部-</option>';
                    var html = html_default + result.data;
                    $("#city").html(html);
                    $("#district").html(html_default);
                    $(".select2").select2({language: 'zh-CN'});
                } else {
                    alert(result.errorMsg);
                }
            }
        });
        <?php  if (!empty($model->city_code)) {?>
        <?php if (!empty($model->district_code)) {?>
        const  area_data = {area_code: <?php echo $model->city_code;?>, select_code: <?php echo empty($model->district_code) ? '' : $model->district_code;?>}
        <?php } else {?>
        const  area_data = {area_code: <?php echo $model->city_code;?>}

        <?php }?>

        $.ajax({
            type: "get",
            url: "/institution/cnarea/ajax-get-list",
            cache: false,
            data: area_data,
            dataType: 'json',
            success: function (result) {
                console.log(result);
                if (result.status == 1) {
                    var html_default = '<option value="">-全部-</option>';
                    var html = html_default + result.data;
                    $("#district").html(html);
                    $(".select2").select2({language: 'zh-CN'});
                } else {
                    alert(result.errorMsg);
                }
            }
        });
        <?php }?>
    });
    <?php }?>

    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
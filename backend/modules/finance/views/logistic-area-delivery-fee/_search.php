<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
\backend\assets\Select2Asset::register($this);

/** @var yii\web\View $this */
/** @var common\models\LogisticAreaDeliveryFeeSearch $model */
/** @var yii\widgets\ActiveForm $form */
/* @var $cityList array */
/* @var $districtList array */
?>

<div class="logistic-area-delivery-fee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <?= $form->field($model, 'logistic_id', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompany::getAll(), 'id', 'company_name'), ['prompt' => '-全部-', 'class'=>'form-control select2']); ?>
        <?= $form->field($model, 'province', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Cnarea::getAllByLevel(\common\models\Cnarea::LEVEL_ONE), 'area_code', 'name'), ['prompt' => '-全部-', 'class'=>'form-control select2', 'id' => 'province']); ?>
        <?= $form->field($model, 'city', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map($cityList, 'area_code', 'name'), ['prompt' => '-全部-', 'class'=>'form-control select2', 'id' => 'city']); ?>
        <?= $form->field($model, 'district', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map($districtList, 'area_code', 'name'), ['prompt' => '-全部-', 'class'=>'form-control select2', 'id' => 'district']); ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
    <?php $this->beginBlock('js') ?>

    $(function () {
        $(".select2").select2({language:'zh-CN'});

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
        $('#city').change(function(){
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
                        $(".select2").select2({language:'zh-CN'});
                    }else{
                        alert(result.errorMsg);
                    }
                }
            });

        });
    });
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

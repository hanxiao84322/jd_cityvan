<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrder $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-settlement-order-form">
    <div class="box">
        <div class="box-body">

            <p><h2>对账单信息</h2></p>
            <?= \yii\widgets\DetailView::widget([
                'model' => $logisticCompanyCheckBillModel,
                'attributes' => [
                    'logistic_company_check_bill_no',
                    [
                        'attribute' => 'logistic_id',
                        'value' =>
                            function ($model) {
                                return \common\models\LogisticCompany::getNameById($model->logistic_id);
                            },
                    ],
                    'warehouse_code',
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return \common\models\LogisticCompanyCheckBill::getTypeName($model->type);
                        }
                    ],
                    'date',
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($model) {
                                return \common\models\LogisticCompanyCheckBill::getStatusName($model->status);
                            },
                    ],
                    'logistic_company_order_num',
                    'system_order_num',
                    [
                        'label' => '差异单量',
                        'format' => 'raw',
                        'value' =>
                            function ($model) {
                                return "<b style='color: red;'>" . $model->logistic_company_order_num - $model->system_order_num . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . Html::a('下载差异单', ['/finance/logistic-company-check-bill-detail/export-data', 'LogisticCompanyCheckBillDetailSearch[logistic_company_check_bill_no]' => $model->logistic_company_check_bill_no, 'is_diff' => 1], ['target' => '_blank']);;
                            },
                    ],
                    'logistic_company_order_price',
                    [
                        'label' => '有效金额',
                        'format' => 'raw',
                        'value' =>
                            function ($model) {
                                return $model->system_order_price . "<input id='system_order_price' value='" . $model->system_order_price . "' type='hidden' />";
                            },
                    ],
                    [
                        'label' => '差异金额',
                        'format' => 'raw',
                        'value' =>
                            function ($model) {
                                return "<b style='color: red;'>" . $model->logistic_company_order_price - $model->system_order_price . "</b>";
                            },
                    ],
                    'create_username',
                    'create_time',
                    'update_username',
                    'update_time',
                    'note:ntext',
                ],
            ]) ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <p>
            <h2>结算单信息</h2></p>
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <?= $form->field($model, 'settlement_order_no', ['options' => ['class' => 'col-xs-3']])->textInput(['readonly' => 'readonly']); ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'type', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanySettlementOrder::$typeList, ['disabled' => 'disabled']); ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'order_num', ['options' => ['class' => 'col-xs-3']])->textInput(); ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'diff_adjust_plan', ['options' => ['class' => 'col-xs-3', 'id' => 'diff_adjust_plan']])->dropDownList(\common\models\LogisticCompanySettlementOrder::$diffAdjustPlanList); ?>
            </div>
            <div class="row" id="input_amount_input" style="display: none;">
                <?= $form->field($model, 'input_amount', ['options' => ['class' => 'col-xs-3']])->textInput(); ?>
            </div>
            <div class="row">

                <div id="adjust_term_div" class="col-xs-8">
                    <?php if (!$model->isNewRecord) {?>
                    <?php echo $adjustTermList; ?>
                    <?php } ?>
                </div>

            </div>
            <div class="row">
                <div>
                    <?= Html::a('新增调整项', '#', [
                        'class' => 'col-xs-3',
                        'data-toggle' => 'modal',
                        'data-target' => '#page-modal'    //此处对应Modal组件中设置的id
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <?= $form->field($model, 'discounts_reductions', ['options' => ['class' => 'col-xs-3']])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\LogisticCompanySettlementOrderDiscountsReductions::getAll(), 'id', 'name'), ['prompt' => '---无优惠---'])->label('优惠方案'); ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'expect_amount', ['options' => ['class' => 'col-xs-3']])->textInput(); ?>
                <?= Html::hiddenInput('expect_amount_source', '', ['id' => 'expect_amount_source']); ?>

            </div>
            <div class="row">
                <?= $form->field($model, 'note', ['options' => ['class' => 'col-xs-3']])->textarea(); ?>

            </div>

            <div class="form-group">
                <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
            </div>
        </div>

    </div>
    <?php ActiveForm::end(); ?>

</div>


<?php

\yii\bootstrap\Modal::begin([
    'id' => 'page-modal',
    'header' => '<h5>调整项信息</h5>',
]);
?>
<?php $form = ActiveForm::begin([
    'method' => 'post',
]); ?>
<p>金额：<?= Html::textInput('amount', '', ['id' => 'adjust_amount']) ?></p>
<p>说明：<?= Html::textarea('content', '', ['id' => 'adjust_content']) ?></p>
<?= Html::button('保存', ['class' => 'btn btn-primary', 'id' => 'create_adjust_term']) ?>
<p style="margin-top: 20px" id="message">
</p>
<?php ActiveForm::end(); ?>
<?php
\yii\bootstrap\Modal::end();

?>

<script>
    <?php $this->beginBlock('js') ?>
    $(function () {
        $('#logisticcompanysettlementorder-diff_adjust_plan').change(function () {
            const diff_adjust_plan = $('#logisticcompanysettlementorder-diff_adjust_plan').val();

            if (diff_adjust_plan == 2) {
                $('#input_amount_input').css('display', 'block');
                $('#logisticcompanysettlementorder-expect_amount').val(0);
            } else {
                $('#input_amount_input').css('display', 'none');
                const system_order_price = $('#system_order_price').val();
                $('#logisticcompanysettlementorder-expect_amount').val(system_order_price);
                $('#expect_amount_source').val(system_order_price);
            }

        });
        $('#logisticcompanysettlementorder-input_amount').on('input', function () {
            const input_amount = $('#logisticcompanysettlementorder-input_amount').val();
            $('#logisticcompanysettlementorder-expect_amount').val(input_amount);
            $('#expect_amount_source').val(input_amount);
            $('#logisticcompanysettlementorder-discounts_reductions').val("");
        });

        $('#create_adjust_term').click(function () {
            const adjust_amount = $("#adjust_amount").val();
            const adjust_content = $("#adjust_content").val();

            const adjust_term_p = "<div class=\"adjust-term-container\">调整金额：<input name='adjust_amount[]' type='text' value='" + adjust_amount + "'  class='adjust_amount_list'>元&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;说明：<input name='adjust_content[]'  type='text'  value='" + adjust_content + "' class='adjust_content_list'>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick='delete_adjust_term(event)'>删除</a></div><br>";
            $("#adjust_term_div").append(adjust_term_p);
            const expect_amount = $('#logisticcompanysettlementorder-expect_amount').val();

            $('#logisticcompanysettlementorder-expect_amount').val(parseInt(expect_amount) + parseInt(adjust_amount));
            $('#expect_amount_source').val(parseInt(expect_amount) + parseInt(adjust_amount));

            $("#adjust_amount").val("");
            $("#adjust_content").val("");
            $("#page-modal").modal("hide");
            $('#logisticcompanysettlementorder-discounts_reductions').val("");

        });
        $('#logisticcompanysettlementorder-discounts_reductions').change(function () {
            const discounts_reductions_id = $('#logisticcompanysettlementorder-discounts_reductions').val();
            console.log(discounts_reductions_id);
            const expect_amount = $('#logisticcompanysettlementorder-expect_amount').val();
            const expect_amount_source = $('#expect_amount_source').val();
            if (discounts_reductions_id == '') {
                $('#logisticcompanysettlementorder-expect_amount').val(expect_amount_source);
            } else{
                $.ajax({
                    url: '/finance/logistic-company-settlement-order-discounts-reductions/ajax-get-amount',
                    cache: false,
                    dataType: 'json',
                    type: 'POST',
                    data: {'discounts_reductions_id': discounts_reductions_id, 'expect_amount': expect_amount},
                    success: function (result) {
                        console.log(result);
                        if (result.status == 0) {
                            alert(result.errMsg);
                        } else {
                            $('#logisticcompanysettlementorder-expect_amount').val(parseInt(result.discounts_reductions_amount));
                            $('#expect_amount_source').val(parseInt(result.discounts_reductions_amount));

                        }
                    }
                });
            }


        });


    });


    function delete_adjust_term(event) {
        event.preventDefault();
        var container = $(event.target).closest('.adjust-term-container');
        var adjust_amount_value = container.find('.adjust_amount_list').val();
        console.log(adjust_amount_value);
        const expect_amount = $('#logisticcompanysettlementorder-expect_amount').val();

        $('#logisticcompanysettlementorder-expect_amount').val(parseInt(expect_amount) - parseInt(adjust_amount_value));
        $('#expect_amount_source').val(parseInt(expect_amount) - parseInt(adjust_amount_value));
        container.remove();
        $('#logisticcompanysettlementorder-discounts_reductions').val("");
    }

    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
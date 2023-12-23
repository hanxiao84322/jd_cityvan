<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBill $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-check-bill-form">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'logistic_company_check_bill_no',
            [
                'label' => '快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            'warehouse_code',
            [
                'label' => '类型',
                'value' => function ($model) {
                    return \common\models\LogisticCompanyCheckBill::getTypeName($model->type);
                }
            ],
            'date',
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
            'system_order_price',
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

        ],
    ]) ?>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <?= $form->field($model, 'note', ['options' => ['class' => 'col-xs-3']])->textarea(['rows' => 6]) ?>
    </div>
    <div class="row">
    <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\LogisticCompanyCheckBill::$statusList, ['prompt' => '-全部-', 'class' => 'form-control'])->label('状态'); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

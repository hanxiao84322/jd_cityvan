<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillDetailSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="logistic-company-check-bill-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'search_form',
    ]); ?>
    <div class="row">

    <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->label('状态')->dropDownList(\common\models\LogisticCompanyCheckBillDetail::$statusList, ['prompt' => '---全部---']) ?>
    <?= $form->field($model, 'is_diff_status', ['options' => ['class' => 'col-xs-4']])->label('只看异常单')->checkboxList([1 => '']) ?>
    <?= Html::hiddenInput('LogisticCompanyCheckBillDetailSearch[logistic_company_check_bill_no]', $model->logistic_company_check_bill_no) ?>


    </div>
    <div class="form-group">
        <div class="form-group">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'onclick' => 'return searchForm();']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::button('导出', ['class' => 'btn btn-info', 'onclick' => 'return exportDataForm();', 'style' => 'margin-left:15px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
    <script>
        <?php $this->beginBlock('js') ?>
        function exportDataForm() {
            $('#search_form').attr('action','/index.php/finance/logistic-company-check-bill-detail/export-data');
            $('#search_form').submit();
        }
        function searchForm() {
            $('#search_form').attr('action','/index.php/finance/logistic-company-check-bill-detail/index');
            $('#search_form').submit();
        }
        <?php $this->endBlock() ?>
        <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
    </script>

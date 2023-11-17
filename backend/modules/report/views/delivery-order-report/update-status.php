<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrder $model */

$this->title = '修改状态';
$this->params['breadcrumbs'][] = ['label' => '未达到最终状态预警报表', 'url' => ['//report/delivery-order-report/final-status-warning']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="delivery-order-update">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?= $form->field($model, 'status', ['options' => ['class' => 'col-xs-3']])->dropDownList(\common\models\DeliveryOrder::$statusList); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>

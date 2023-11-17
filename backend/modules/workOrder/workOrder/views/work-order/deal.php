<?php

use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */

$this->title = '处理工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="work-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'work_order_no',
            'order_no',
            [
                'attribute' => 'type',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getTypeName($model->type);
                    },
            ],
            [
                'attribute' => 'priority',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getPriorityName($model->priority);
                    },
            ],
            'receive_name',
            'receive_phone',
            'receive_address',
            'operate_username',
            'description',
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getStatusName($model->status);
                    },
            ],
            'create_time',
            'create_username',
            'update_time',
            'update_username',
            'finished_time',
        ],
    ]) ?>
    <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <?= $form->field($model, 'content', ['options' => ['class' => 'col-xs-4']])->textarea() ?>
        </div>
<!--        <div class="row">-->
            <?php // echo $form->field($model, 'file_path', ['options' => ['class' => 'col-xs-4']])->fileInput(['maxlength' => true]) ?>
<!--        </div>-->
        <div class="form-group">
            <?= \yii\helpers\Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

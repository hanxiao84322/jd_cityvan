<?php

use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\bootstrap\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\WorkOrder $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '处理工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="work-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'logistic_no',
            'work_order_no',
            'order_no',
            'warehouse_code',
            'jd_work_order_no',
            'assign_username',
            'operate_username',
            [
                'attribute' => 'logistic_id',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            [
                'attribute' => 'type',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrderType::getTypeName($model->type);
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
            'order_create_num',
            [
                'attribute' => 'system_create',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getCreateName($model->system_create);
                    },
            ],
            [
                'attribute' => 'ordinary_create',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getCreateName($model->ordinary_create);
                    },
            ],
            [
                'attribute' => 'jd_create',
                'value' =>
                    function ($model) {
                        return \common\models\WorkOrder::getCreateName($model->jd_create);
                    },
            ],
            'penalty_amount',
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

    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $orderFilesSearchDataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'name',
                    'create_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'position: sticky; right: -11px; background-color: #ffffff'],
                        'header' => '操作',
                        'template' => '{download} {delete}',
                        'buttons' => [
                            'download' => function ($url, $model) {
                                return Html::a('下载', 'download-order-file?id=' . $model->id, ['target' => '_blank']);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('删除', 'delete-order-file?id=' . $model->id, [
                                    'data' => [
                                        'confirm' => '确定要删除该附件吗?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    'reply_time',
                    'reply_name',
                    'reply_content',
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($workOrderReplyModel) {
                                return \common\models\WorkOrder::getStatusName($workOrderReplyModel->status);
                            },
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-xs-4 field-workorder-content">
            <label class="control-label" for="workorder-content">处理说明</label>
            <textarea id="workorder-content" class="form-control" name="reply_content" aria-invalid="false"></textarea>
            <input type="hidden" value="<?php echo $model->work_order_no; ?>" name="work_order_no">
            <div class="help-block"></div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <?php
            echo $form->field($model, 'files[]', ['options' => ['class' => 'col-xs-4']])->fileInput(['multiple' => true])->label('上传附件');
            ?>
        </div>
        <?= \yii\helpers\Html::submitButton('回复', ['class' => 'btn btn-success', 'name' => 'reply_submit']) ?>
        <?php if (Yii::$app->user->getIdentity()['type'] == \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) { ?>
            <?= \yii\helpers\Html::submitButton('挂起', ['class' => 'btn btn-success', 'name' => 'pending_submit']) ?>
            <?= \yii\helpers\Html::submitButton('处理完成', ['class' => 'btn btn-success', 'name' => 'deal_finish_submit']) ?>
        <?php } else { ?>
            <?php if ($model->status != \common\models\WorkOrder::STATUS_FINISHED) { ?>
                <?= \yii\helpers\Html::submitButton('确认完成', ['class' => 'btn btn-success', 'name' => 'finished_submit']) ?>
                <?= \yii\helpers\Html::submitButton('未完成继续处理', ['class' => 'btn btn-success', 'name' => 'deal_submit']) ?>
            <?php } else { ?>

                <?= \yii\helpers\Html::submitButton('打开工单', ['class' => 'btn btn-success', 'name' => 'deal_submit']) ?>
            <?php } ?>

        <?php } ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>

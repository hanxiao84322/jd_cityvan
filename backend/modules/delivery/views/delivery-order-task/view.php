<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderTask $model */

$this->title = '任务详情';
$this->params['breadcrumbs'][] = ['label' => '任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="delivery-order-task-view">

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['style' => ['width' => '1200px;']],
        'attributes' => [
            'id',
            'file_path',
            [
                'label' => '类型',
                'value' => function ($model) {
                    return \common\models\DeliveryOrderTask::getTypeName($model->type);
                }
            ],
            [
                'label' => '状态',
                'value' => function ($model) {
                    return \common\models\DeliveryOrderTask::getStatusName($model->status);
                }
            ],
            [
                'label' => '执行结果',
                'format' => 'raw',
                'contentOptions' => ['style' => ['width' => '1000px']],
                'value' => function ($model) {
                    return $model->result;
                }
            ],
            [
                'label' => '错误数据',
                'format' => 'raw',
                'contentOptions' => ['style' => ['width' => '1000px']],
                'value' => function ($model) {
                    return \common\models\DeliveryOrderTask::getErrorDataHtml($model->error_data);
                }
            ],
            'apply_username',
            'apply_time',
            'end_time',
        ],
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\BaseCost $model */

$this->title = '基础成本详情';
$this->params['breadcrumbs'][] = ['label' => '基础成本列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="base-cost-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'warehouse',
                'value' =>
                    function ($model) {
                        return \common\models\BaseCost::getWarehouseName($model->warehouse);
                    },
            ],
            'month',
            'data_service_fee',
            'month_rent',
            'worker_num',
            'worker_fee',
            'device_fee',
            'create_name',
            'create_time',
            'update_name',
            'update_time',
        ],
    ]) ?>

</div>

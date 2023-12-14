<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\AdjustOrder $model */

$this->title = '查看调整单详情';
$this->params['breadcrumbs'][] = ['label' => '调整单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="adjust-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer_id',
                'value' =>
                    function ($model) {
                        return \common\models\Customer::getNameById($model->customer_id);
                    },
            ],
            'settlement_no',
            'adjust_amount',
            [
                'attribute' => 'type',
                'value' =>
                    function ($model) {
                        return \common\models\AdjustOrder::getTypeName($model->type);
                    },
            ],
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\AdjustOrder::getStatusName($model->status);
                    },
            ],
            'note',
            'create_time',
            'create_name',
        ],
    ]) ?>

</div>

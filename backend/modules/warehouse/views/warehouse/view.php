<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Warehouse $model */

$this->title = '查看仓库详情';
$this->params['breadcrumbs'][] = ['label' => '仓库列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="warehouse-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'code',
            'contact_name',
            'contact_phone',
            'address',
            [
                'attribute' => 'status',
                'value' =>
                    function ($model) {
                        return \common\models\Warehouse::getStatusName($model->status);
                    },
            ],
        ],
    ]) ?>
</div>

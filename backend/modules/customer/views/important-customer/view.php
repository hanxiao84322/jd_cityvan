<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ImportantCustomer $model */

$this->title = '查看重点客户';
$this->params['breadcrumbs'][] = ['label' => '重点客户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="important-customer-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'phone',
            'address',
            'complaint_type',
            'work_order_num',
            [
                'attribute' => 'level',
                'value' =>
                    function ($model) {
                        return \common\models\ImportantCustomer::getLevelName($model->level);
                    },
            ],
            'create_time',
            'create_name',
            'update_time',
            'update_name',
        ],
    ]) ?>

</div>

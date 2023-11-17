<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\WarehouseAreaDeliveryFee $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Warehouse Area Delivery Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="warehouse-area-delivery-fee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'warehouse_id',
            'province',
            'city',
            'district',
            'weight',
            'price',
            'follow_weight',
            'follow_price',
            'return_rate',
            'agent_rate',
            'is_cancel',
            'create_user',
            'create_time',
            'update_user',
            'update_time',
        ],
    ]) ?>

</div>

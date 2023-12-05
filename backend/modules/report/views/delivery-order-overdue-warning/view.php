<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderOverdueWarning $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Delivery Order Overdue Warnings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="delivery-order-overdue-warning-view">

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
            'date',
            'warehouse_code',
            'logistic_id',
            'less_one_day',
            'one_to_two_day',
            'two_to_three_day',
            'three_to_five_day',
            'five_to_seven_day',
            'more_seven_day',
        ],
    ]) ?>

</div>

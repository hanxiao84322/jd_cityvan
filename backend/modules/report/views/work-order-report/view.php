<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiency $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Service Daily Efficiencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-service-daily-efficiency-view">

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
            'username',
            'type',
            'work_order_create_num',
            'work_order_deal_num',
            'work_order_finished_num',
            'work_order_not_finished_num',
            'work_order_finished_rate',
        ],
    ]) ?>

</div>

<?php

use common\models\WarehouseLogisticAreaDeliveryFee;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\vSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Warehouse Logistic Area Delivery Fees';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-logistic-area-delivery-fee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Warehouse Logistic Area Delivery Fee', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'warehouse_id',
            'logistic_id',
            'province',
            'city',
            //'district',
            //'weight',
            //'price',
            //'follow_weight',
            //'follow_price',
            //'return_rate',
            //'agent_rate',
            //'is_cancel',
            //'create_user',
            //'create_time',
            //'update_user',
            //'update_time',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, WarehouseLogisticAreaDeliveryFee $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

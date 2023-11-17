<?php

use common\models\CustomerServiceDailyEfficiency;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerServiceDailyEfficiencySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Customer Service Daily Efficiencies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-service-daily-efficiency-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Customer Service Daily Efficiency', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date',
            'username',
            'type',
            'work_order_create_num',
            //'work_order_deal_num',
            //'work_order_finished_num',
            //'work_order_not_finished_num',
            //'work_order_finished_rate',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CustomerServiceDailyEfficiency $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

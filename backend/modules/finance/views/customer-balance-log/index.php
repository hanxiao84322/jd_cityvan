<?php

use common\models\CustomerBalanceLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalanceLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Customer Balance Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-balance-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Customer Balance Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'institution_id',
            'customer_id',
            'before_balance',
            'after_balance',
            //'change_amount',
            //'source',
            //'type',
            //'category',
            //'change_time',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CustomerBalanceLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

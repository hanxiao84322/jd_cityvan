<?php

use common\models\LogisticCompanySettlementOrderDiscountsReductions;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDiscountsReductionsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '结算单优惠方案列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-settlement-order-discounts-reductions-index">
    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1110px;white-space:nowrap;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'options' => ['class' => 'hidden']//关闭分页
        ],
        'columns' => [
            'name',
            [
                'header' => '类型',
                'value' => function ($model) {
                    return LogisticCompanySettlementOrderDiscountsReductions::getTypeName($model->type);
                }
            ],
            'min_price',
            'discount',
            'sub_price',
            'create_username',
            'create_time',
            //'update_username',
            //'update_time',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LogisticCompanySettlementOrderDiscountsReductions $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalance $model */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '我的余额';
$this->params['breadcrumbs'][] = ['label' => '客户余额列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-balance-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'institution_id',
                'value' =>
                    function ($model) {
                        return \backend\models\Institution::getNameById($model->institution_id);
                    },
            ],
            [
                'attribute' => 'customer_id',
                'value' =>
                    function ($model) {
                        return \common\models\Customer::getNameById($model->customer_id);
                    },
            ],
            'face_orders_num',
            'balance',
            'last_recharge_time',
            'default_recharge_username',
            'last_operation_detail',
            'last_recharge_notes',
            'update_username',
            'update_time',
        ],
    ]) ?>

</div>
<div class="box">
    <div class="box-body">
        余额变更记录
    </div>
</div>
<div class="box">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pager' => [
                'options' => ['class' => 'hidden']//关闭分页
            ],
            'columns' => [
                'before_balance',
                'after_balance',
                'change_amount',
                'source',
                [
                    'attribute' => 'type',
                    'value' =>
                        function ($model) {
                            return \common\models\CustomerBalanceLog::getTypeName($model->type);
                        },
                ],
                [
                    'attribute' => 'category',
                    'value' =>
                        function ($model) {
                            return \common\models\CustomerBalanceLog::getCategoryName($model->category);
                        },
                ],
                'change_time',
//                [
//                    'class' => 'yii\grid\ActionColumn',
//                    'header' => '操作',
//                    'template' => '{view}  {recharge}',
//                    'buttons' => [
//                        'view' => function ($url, $model) {
//                            return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
//                        },
//                        'recharge' => function ($url, $model) {
//                            return Html::a('充值', 'recharge?id=' . $model->customer_id, ['target' => '_blank']);
//                        },
//
//                    ]
//                ],
            ],
        ]); ?>


    </div>
    <?= \common\widgets\LinkPager::widget([
        'pagination' => $pages,
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'go' => true,
        'totalCount' => isset($pages->totalCount) ? $pages->totalCount : 0
    ]);
    ?>
</div>

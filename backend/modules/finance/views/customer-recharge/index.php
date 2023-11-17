<?php

use backend\models\CustomerRecharge;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\CustomerRechargeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */
/** @var int $institutionId */
/** @var int $level */

$this->title = '客户充值列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-recharge-index">
    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel, 'institutionId' => $institutionId, 'level' => $level]); ?>
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
                    'recharge_order_no',
                    'customer_name',
                    'amount',
                    //'type',
                    //'status',
                    //'notes',
                    //'pay_image_path',
                    //'invoice_image_path',
                    'create_name',
                    'create_time',
                    //'update_name',
                    //'update_time',
                    //'pay_confirm_name',
                    //'pay_confirm_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                        ]
                    ],
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
</div>

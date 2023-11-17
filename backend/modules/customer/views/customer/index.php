<?php

use common\models\Customer;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CustomerSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $institutionId */
/** @var int $level */
/* @var $pages yii\data\ActiveDataProvider */


$this->title = '客户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">
    <div class="belong-city-index">
    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel,'institutionId' => $institutionId,'level' => $level]); ?>
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
            'code',
            'name',
            'institution_name',
            [
                'header' => '类型',
                'format' => 'raw',
                'value' => function ($model) {
                    return Customer::getTypeName($model->type);
                }
            ],
            'delivery_platform',
            'sender_name',
            'sender_phone',
            'sender_company',
            'sender_address',
            //'order_get_type',
            [
                'header' => '状态',
                'format' => 'raw',
                'value' => function ($model) {
                    return Customer::getShowStatusName($model->status);
                }
            ],
            //'code',
//            'create_name',
//            'create_time',
            'update_name',
            'update_time',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view}  {update}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                    },
                    'update' => function($url, $model) {
                        return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                    },

                ]
            ],
        ],
    ]); ?>
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
</div>

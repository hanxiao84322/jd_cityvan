<?php

use common\models\LogisticCompany;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '快递公司列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
            'company_name',
            [
                'header' => '状态',
                'headerOptions' => [
                    'style' => 'text-align:center;'
                ],
                'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center']],
                'format' => 'raw',
                'value' => function ($model) {
                    return LogisticCompany::getStatusName($model->status);
                }
            ],
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

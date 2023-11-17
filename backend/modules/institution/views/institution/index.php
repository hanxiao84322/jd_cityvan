<?php

use backend\models\Institution;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\InstitutionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */
/** @var int $isParent */



$this->title = '组织机构管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="institution-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel, 'isParent' => $isParent]); ?>
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
                    'sort_name',
                    [
                        'attribute' => 'level',
                        'value' =>
                            function ($model) {
                                return Institution::$levelList[$model->level];
                            },
                    ],
                    //'partner_code',
                    'phone',
                    'create_name',
                    'create_time',
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

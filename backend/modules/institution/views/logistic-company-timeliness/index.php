<?php
use yii\helpers\Html;
use yii\grid\GridView;
/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyTimelinessSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '快递公司时效列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-timeliness-index">

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
            'warehouse_code',
            [
                'label' => '快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            [
                'attribute' => 'province',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::getNameByCode($model->province);
                    },
            ],
            [
                'attribute' => 'city',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::getNameByCode($model->city);
                    },
            ],
            [
                'attribute' => 'district',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::getNameByCode($model->district);
                    },
            ],
            'timeliness',
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


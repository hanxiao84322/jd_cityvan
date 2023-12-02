<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\UserBackendSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */


$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-backend-index">
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
                    'username',
                    [
                        'attribute' => 'type',
                        'value' =>
                            function ($model) {
                                return \backend\models\UserBackend::getTypeName($model->type);
                            },
                    ],
                    'name',
                    'email:email',
                    [
                        'attribute' => 'status',
                        'value' =>
                            function ($model) {
                                return \backend\models\UserBackend::getStatusName($model->status);
                            },
                    ],
                    'created_at',
                    'updated_at',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update} {reset_password}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                            },
                            'reset_password' => function ($url, $model) {
                                return Html::a('重置密码', 'update-password?id=' . $model->id, ['target' => '_blank']);
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
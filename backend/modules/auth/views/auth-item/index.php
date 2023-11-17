<?php

use backend\models\AuthItem;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\AuthItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            'type',
            'description:ntext',
            'rule_name',
            'data',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, AuthItem $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'name' => $model->name]);
                 }
            ],
        ],
    ]); ?>


</div>

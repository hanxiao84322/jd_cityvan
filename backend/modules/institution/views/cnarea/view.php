<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Cnarea $model */

$this->title = '区划详情';
$this->params['breadcrumbs'][] = ['label' => '区划管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cnarea-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'area_code',
            [
                'attribute' => 'level',
                'value' =>
                    function ($model) {
                        return \common\models\Cnarea::$levelList[$model->level];
                    },
            ],
            'parent_code',
            'zip_code',
            'city_code',
            'short_name',
            'merger_name',
            'pinyin',
            'lng',
            'lat',
        ],
    ]) ?>

</div>

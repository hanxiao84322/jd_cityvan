<?php

use common\models\DeliveryImage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\DeliveryImageSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '运单图片解析';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-image-index">
    <div class="box">
        <div class="box-body">
            <div class="form-group">
                <?= Html::a('手动解析', ['update-receiver-info-by-image'], ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'logistic_no',
                    [
                        'header' => '解析内容',
                        'headerOptions' => [
                            'style' => 'text-align:center'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center', 'width' => '500px;']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryImage::getImageData($model->image_data);
                        }
                    ],
                    'create_time',
                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, DeliveryImage $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        }
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>

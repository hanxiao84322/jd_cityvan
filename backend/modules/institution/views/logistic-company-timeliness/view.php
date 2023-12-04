<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyTimeliness $model */

$this->title = '快递公司时效详情';
$this->params['breadcrumbs'][] = ['label' => '快递公司时效列表', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-timeliness-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'warehouse_code',
            [
                'label' => '快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            'province',
            'city',
            'district',
            'timeliness',
        ],
    ]) ?>

</div>

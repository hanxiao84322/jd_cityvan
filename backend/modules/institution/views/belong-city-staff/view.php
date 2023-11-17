<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\BelongCityStaff $model */

$this->title = '员工对照详情';
$this->params['breadcrumbs'][] = ['label' => '员工对照列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="belong-city-staff-view">
    <div class="row">
        <?= \common\components\LayoutHelper::boxBegin('员工对照详情') ?>
        <table class="table table-bordered " style="width:50%">
            <tr>
                <td><b>厅点</b></td>
                <td><?= \backend\models\BelongCity::getNameById($model->belong_city_id); ?></td>
            </tr>
            <tr>
                <td><b>员工编码</b></td>
                <td><?= $model->code ?></td>
            </tr>
            <tr>
                <td><b>员工姓名</b></td>
                <td><?= $model->name ?></td>
            </tr>
        </table>
        <?= \common\components\LayoutHelper::boxEnd() ?>
    </div>

</div>

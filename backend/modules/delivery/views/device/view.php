<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Device $model */

$this->title = '设备详情';
$this->params['breadcrumbs'][] = ['label' => '设备列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="device-view">
    <div class="row">
    <?= \common\components\LayoutHelper::boxBegin('设备详情') ?>
    <table class="table table-bordered " style="width:50%">
        <tr>
            <td><b>编码</b></td>
            <td><?= $model->code ?></td>
        </tr>
        <tr>
            <td><b>厅点</b></td>
            <td><?= \backend\models\BelongCity::getNameById($model->belong_city_id); ?></td>
        </tr>
        <tr>
            <td><b>方向</b></td>
            <td><?= $model->direction ?></td>
        </tr>
    </table>
    <?= \common\components\LayoutHelper::boxEnd() ?>
</div>


</div>

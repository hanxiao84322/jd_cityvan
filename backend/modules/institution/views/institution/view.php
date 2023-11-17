<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\Institution $model */

$this->title = '组织机构详情';
$this->params['breadcrumbs'][] = ['label' => '组织机构管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="institution-view">
    <div class="row">
        <?= \common\components\LayoutHelper::boxBegin('组织机构详情') ?>
        <table class="table table-bordered " style="width:50%">
            <tr>
                <td><b>编码</b></td>
                <td><?=$model->code?></td>
            </tr>
            <tr>
                <td><b>名称</b></td>
                <td><?=$model->code?></td>
            </tr>
            <tr>
                <td><b>简称</b></td>
                <td><?=$model->sort_name?></td>
            </tr>
            <tr>
                <td><b>类型</b></td>
                <td><?= \backend\models\Institution::$levelList[$model->level]; ?></td>
            </tr>
            <tr>
                <td><b>上级组织机构</b></td>
                <td><?=\backend\models\Institution::getNameById($model->parent_id)?></td>
            </tr>
            <tr>
                <td><b>厅点</b></td>
                <td><?=\backend\models\BelongCity::getListByJsonId($model->belong_city_list)?></td>
            </tr>
            <tr>
                <td><b>联系电话</b></td>
                <td><?=$model->phone?></td>
            </tr>
            <tr>
                <td><b>简介</b></td>
                <td><?=$model->content?></td>
            </tr>
            <tr>
                <td><b>创建时间</b></td>
                <td><?=$model->create_time?></td>
            </tr>
            <tr>
                <td><b>创建人</b></td>
                <td><?=$model->create_name?></td>
            </tr>
            <tr>
                <td><b>最后更新时间</b></td>
                <td><?=$model->update_time == '0000-00-00 00:00:00' ? '' : $model->update_time?></td>
            </tr>
            <tr>
                <td><b>最后更新人</b></td>
                <td><?=$model->update_name?></td>
            </tr>
        </table>
        <?= \common\components\LayoutHelper::boxEnd() ?>
    </div>
</div>
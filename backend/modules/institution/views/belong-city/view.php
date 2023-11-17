<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\BelongCity $model */

$this->title = '厅点详情';
$this->params['breadcrumbs'][] = ['label' => '厅点管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="row">
        <?= \common\components\LayoutHelper::boxBegin('查看厅点') ?>
        <table class="table table-bordered text-center">
            <tr>
                <th>名称</th>
                <th>状态</th>
            </tr>
            <tr>
                <td style="vertical-align: middle"><?= $model->name; ?></td>
                <td style="vertical-align: middle"><?= \backend\models\BelongCity::getShowStatusName($model->status); ?></td>
            </tr>
        </table>
        <?= \common\components\LayoutHelper::boxEnd() ?>
    </div>



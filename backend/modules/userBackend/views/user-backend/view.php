<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\UserBackend $model */

$this->title = '用户详情';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-backend-view">

    <div class="row">
        <?= \common\components\LayoutHelper::boxBegin('用户详情') ?>
        <table class="table table-bordered " style="width:50%">
            <tr>
                <td><b>用户名</b></td>
                <td><?=$model->username?></td>
            </tr>
            <tr>
                <td><b>姓名</b></td>
                <td><?=$model->name?></td>
            </tr>
            <tr>
                <td><b>电话</b></td>
                <td><?=$model->phone?></td>
            </tr>
            <tr>
                <td><b>邮箱</b></td>
                <td><?=$model->email?></td>
            </tr>
            <tr>
                <td><b>类型</b></td>
                <td><?=\backend\models\UserBackend::getTypeName($model->type)?></td>
            </tr>
            <tr>
                <td><b>仓库编码</b></td>
                <td><?=!empty($model->warehouse_code_list) ? implode(",",json_decode($model->warehouse_code_list, true)) : ''?></td>
            </tr>
            <tr>
                <td><b>快递公司</b></td>
                <td><?=\common\models\LogisticCompany::getListByJsonId($model->logistic_id_list)?></td>
            </tr>
            <tr>
                <td><b>创建时间</b></td>
                <td><?= $model->created_at; ?></td>
            </tr>
            <tr>
                <td><b>修改时间</b></td>
                <td><?= $model->updated_at; ?></td>
            </tr>
        </table>
        <?= \common\components\LayoutHelper::boxEnd() ?>
    </div>
</div>

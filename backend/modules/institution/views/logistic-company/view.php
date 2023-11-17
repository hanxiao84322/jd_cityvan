<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompany $model */

$this->title = '快递公司详情';
$this->params['breadcrumbs'][] = ['label' => '快递公司管理', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-view">

    <div class="row">
        <?= \common\components\LayoutHelper::boxBegin('快递公司详情') ?>
        <table class="table table-bordered " style="width:50%">
            <tr>
                <td><b>名称</b></td>
                <td><?=$model->company_name?></td>
            </tr>
            <tr>
                <td><b>负责区域</b></td>
                <td><?=$model->responsible_area?></td>
            </tr>
            <tr>
                <td><b>状态</b></td>
                <td><?=\common\models\LogisticCompany::getStatusName($model->status);?></td>
            </tr>
        </table>
        <?= \common\components\LayoutHelper::boxEnd() ?>
    </div>

</div>

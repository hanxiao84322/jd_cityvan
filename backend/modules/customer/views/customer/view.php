<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Customer $model */

$this->title = '客户详情';
$this->params['breadcrumbs'][] = ['label' => '客户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-view">

    <div class="row">
        <div class="row">
            <?= \common\components\LayoutHelper::boxBegin('客户详情') ?>
            <table class="table table-bordered " style="width:50%">
                <tr>
                    <td><b>名称</b></td>
                    <td><?=$model->name?></td>
                </tr>
                <tr>
                    <td><b>组织机构</b></td>
                    <td><?=\backend\models\Institution::getNameById($model->institution_id)?></td>
                </tr>
                <tr>
                    <td><b>上级客户</b></td>
                    <td><?=\common\models\Customer::getNameById($model->parent_customer_id)?></td>
                </tr>
                <tr>
                    <td><b>发货平台</b></td>
                    <td><?=$model->delivery_platform?></td>
                </tr>
                <tr>
                    <td><b>寄件人姓名</b></td>
                    <td><?=$model->sender_name?></td>
                </tr>
                <tr>
                    <td><b>寄件人联系电话</b></td>
                    <td><?=$model->sender_phone?></td>
                </tr>
                <tr>
                    <td><b>寄件人公司</b></td>
                    <td><?=$model->sender_company?></td>
                </tr>
                <tr>
                    <td><b>寄件人地址</b></td>
                    <td><?=$model->sender_address?></td>
                </tr>
                <tr>
                    <td><b>订单获取方式</b></td>
                    <td><?=$model->order_get_type?></td>
                </tr>
                <tr>
                    <td><b>运费方式</b></td>
                    <td style="vertical-align: middle"><?= \common\models\Customer::getFreeTypeName($model->free_type); ?></td>
                </tr>
                <tr>
                    <td><b>状态</b></td>
                    <td style="vertical-align: middle"><?= \common\models\Customer::getShowStatusName($model->status); ?></td>
                </tr>
                <tr>
                    <td><b>编码</b></td>
                    <td><?=$model->code?></td>
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

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanySettlementOrderDiscountsReductions $model */

$this->title = '结算单优惠方案详情';
$this->params['breadcrumbs'][] = ['label' => '结算单优惠方案列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-settlement-order-discounts-reductions-view">

    <h2>方案名称：<?= Html::encode($model->name) ?></h2>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => '类型',
                'value' => function ($model) {
                    return \common\models\LogisticCompanySettlementOrderDiscountsReductions::getTypeName($model->type);
                }
            ],
            'min_price',
            'discount',
            'sub_price',
            'content:ntext',
            'create_username',
            'create_time',
            'update_username',
            'update_time',
        ],
    ]) ?>

</div>

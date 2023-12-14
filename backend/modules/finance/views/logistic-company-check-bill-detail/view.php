<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillDetail $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Check Bill Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-check-bill-detail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'logistic_company_check_bill_no',
            'warehouse_code',
            'logistic_id',
            'logistic_no',
            'weight',
            'price',
            'system_weight',
            'system_price',
            'status',
            'note:ntext',
            'create_username',
            'create_time',
        ],
    ]) ?>

</div>

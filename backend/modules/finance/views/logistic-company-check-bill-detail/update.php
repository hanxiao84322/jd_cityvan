<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillDetail $model */

$this->title = 'Update Logistic Company Check Bill Detail: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Check Bill Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistic-company-check-bill-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

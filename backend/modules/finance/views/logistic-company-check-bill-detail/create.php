<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBillDetail $model */

$this->title = 'Create Logistic Company Check Bill Detail';
$this->params['breadcrumbs'][] = ['label' => 'Logistic Company Check Bill Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-check-bill-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyCheckBill $model */

$this->title = '修改对账单';
$this->params['breadcrumbs'][] = ['label' => '对账单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-check-bill-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRules $model */

$this->title = '修改运费规则';
$this->params['breadcrumbs'][] = ['label' => '运费规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-fee-rules-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

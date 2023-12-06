<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRules $model */

$this->title = '修改快递公司运费';
$this->params['breadcrumbs'][] = ['label' => '快递公司运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-fee-rules-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

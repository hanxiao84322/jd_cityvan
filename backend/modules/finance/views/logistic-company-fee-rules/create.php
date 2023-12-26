<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRules $model */

$this->title = '新增运费规则';
$this->params['breadcrumbs'][] = ['label' => '运费规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-fee-rules-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

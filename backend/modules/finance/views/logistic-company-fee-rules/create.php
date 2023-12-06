<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRules $model */

$this->title = '新增快递公司运费';
$this->params['breadcrumbs'][] = ['label' => '快递公司运费列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-fee-rules-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyTimeliness $model */

$this->title = '修改快递公司时效';
$this->params['breadcrumbs'][] = ['label' => '快递公司时效列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-timeliness-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyTimeliness $model */

$this->title = '新增快递公司时效';
$this->params['breadcrumbs'][] = ['label' => '快递公司时效列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-timeliness-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

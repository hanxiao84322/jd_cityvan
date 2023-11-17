<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompany $model */

$this->title = '修改快递公司';
$this->params['breadcrumbs'][] = ['label' => '快递公司管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="logistic-company-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompany $model */

$this->title = '新增快递公司';
$this->params['breadcrumbs'][] = ['label' => '快递公司管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

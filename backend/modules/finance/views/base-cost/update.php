<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\BaseCost $model */

$this->title = '修改基础成本';
$this->params['breadcrumbs'][] = ['label' => '基础成本管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="base-cost-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

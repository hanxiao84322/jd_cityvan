<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\BaseCost $model */

$this->title = '新增基础成本';
$this->params['breadcrumbs'][] = ['label' => '基础成本列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="base-cost-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

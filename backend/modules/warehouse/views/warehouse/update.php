<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Warehouse $model */

$this->title = '修改仓库';
$this->params['breadcrumbs'][] = ['label' => '仓库列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改仓库';
?>
<div class="warehouse-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

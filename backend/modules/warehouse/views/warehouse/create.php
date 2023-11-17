<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Warehouse $model */

$this->title = '新建仓库';
$this->params['breadcrumbs'][] = ['label' => '仓库列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

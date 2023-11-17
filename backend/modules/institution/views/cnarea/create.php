<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Cnarea $model */

$this->title = '新增区划';
$this->params['breadcrumbs'][] = ['label' => '区划管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cnarea-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Cnarea $model */

$this->title = '修改区划';
$this->params['breadcrumbs'][] = ['label' => '区划管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="cnarea-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

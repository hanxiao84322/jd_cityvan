<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Device $model */

$this->title = '新增设备';
$this->params['breadcrumbs'][] = ['label' => '设备管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BelongCityStaff $model */

$this->title = '修改: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '员工对照列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="belong-city-staff-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

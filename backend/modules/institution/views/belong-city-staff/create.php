<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BelongCityStaff $model */

$this->title = '新增员工对照';
$this->params['breadcrumbs'][] = ['label' => '员工对照列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="belong-city-staff-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

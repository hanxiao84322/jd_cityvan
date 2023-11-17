<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryInfo $model */

$this->title = 'Update Delivery Info: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Delivery Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="delivery-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

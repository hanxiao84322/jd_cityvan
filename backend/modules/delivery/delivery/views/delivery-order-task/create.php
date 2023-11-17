<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderTask $model */

$this->title = 'Create Delivery Order Task';
$this->params['breadcrumbs'][] = ['label' => 'Delivery Order Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

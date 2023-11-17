<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryInfo $model */

$this->title = 'Create Delivery Info';
$this->params['breadcrumbs'][] = ['label' => 'Delivery Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

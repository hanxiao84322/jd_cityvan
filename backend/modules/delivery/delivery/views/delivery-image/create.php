<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeliveryImage $model */

$this->title = 'Create Delivery Image';
$this->params['breadcrumbs'][] = ['label' => 'Delivery Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-image-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

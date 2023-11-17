<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticImage $model */

$this->title = 'Update Logistic Image: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistic Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistic-image-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

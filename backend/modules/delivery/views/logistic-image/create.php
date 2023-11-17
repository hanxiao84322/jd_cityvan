<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LogisticImage $model */

$this->title = 'Create Logistic Image';
$this->params['breadcrumbs'][] = ['label' => 'Logistic Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-image-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

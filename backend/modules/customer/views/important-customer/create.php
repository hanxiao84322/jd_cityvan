<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ImportantCustomer $model */

$this->title = 'Create Important Customer';
$this->params['breadcrumbs'][] = ['label' => 'Important Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="important-customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CustomerBalance $model */

$this->title = 'Create Customer Balance';
$this->params['breadcrumbs'][] = ['label' => 'Customer Balances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-balance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

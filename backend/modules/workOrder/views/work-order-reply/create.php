<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderReply $model */

$this->title = 'Create Work Order Reply';
$this->params['breadcrumbs'][] = ['label' => 'Work Order Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-order-reply-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ApproveLog $model */

$this->title = 'Create Approve Log';
$this->params['breadcrumbs'][] = ['label' => 'Approve Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="approve-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

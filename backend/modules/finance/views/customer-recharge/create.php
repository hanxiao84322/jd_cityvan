<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CustomerRecharge $model */
/** @var int $level */
/** @var int $institutionId */

$this->title = '新增客户充值';
$this->params['breadcrumbs'][] = ['label' => '客户充值列表', 'url' => ['index']];
?>
<div class="customer-recharge-create">

    <?= $this->render('_form', [
        'model' => $model,
        'level' => $level,
        'institutionId' => $institutionId
    ]) ?>

</div>

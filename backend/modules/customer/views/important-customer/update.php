<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ImportantCustomer $model */

$this->title = '修改重点客户';
$this->params['breadcrumbs'][] = ['label' => '重点客户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="important-customer-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\datetime\DateTimePicker;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="delivery-order-search">
    <div class="row">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数据统计时间为<?= date('Y-m-d 00:00:00', strtotime('-7 day'))?>至<?= date('Y-m-d 23:59:59', strtotime('-1 day'))?></p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;无运输：发货后24小时内无运输记录</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;超时运输：有运输记录，但超出发货收间24小时</p>

    </div>
</div>


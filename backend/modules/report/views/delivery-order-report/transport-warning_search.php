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
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数据统计时间为<?= date('Y-m-d 00:00:00', strtotime('-9 day'))?>至<?= date('Y-m-d 23:59:59', strtotime('-1 day'))?></p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;即将超时：当前时间-运输开始时间<12小时</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;无运输结束：运输开始时间超过72小时没有获得运输结束/配置中状态</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;无配送中：有运输结束状态后超过24小时，无配送中状态</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;超时运输结束：获得了运输结束状态，但是获得运输结束状态时间-运输开始状态时间>72</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;超时配送中：获得了配送中状态，但是获得配送中状态的时间-运输结束时间>24</p>
    </div>
</div>


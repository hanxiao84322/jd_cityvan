<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/** @var backend\models\CustomerRecharge $model */

$this->title = '客户充值详情';
$this->params['breadcrumbs'][] = ['label' => '客户充值列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-recharge-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'recharge_order_no',
            [
                'attribute' => 'institution_id',
                'value' =>
                    function ($model) {
                        return \backend\models\Institution::getNameById($model->institution_id);
                    },
            ],
            [
                'attribute' => 'customer_id',
                'value' =>
                    function ($model) {
                        return \common\models\Customer::getNameById($model->customer_id);
                    },
            ],
            'amount',
            'notes',
            [
                'attribute' => 'pay_image_path',
                'format' => 'raw',
                'value' =>
                    function ($model) {
                        return Html::a(Html::img($model->pay_image_path, ["width"=>"80","height"=>"80"]), '#', [
            'data-toggle' => 'modal',
            'data-target' => '#pay_image_path'    //此处对应Modal组件中设置的id
        ]);
                    },
            ],
            [
                'attribute' => 'invoice_image_path',
                'format' => 'raw',
                'visible' => !empty($model->invoice_image_path),
                'value' =>
                    function ($model) {
                        return Html::a(Html::img($model->invoice_image_path, ["width"=>"80","height"=>"80"]), '#', [
                            'data-toggle' => 'modal',
                            'data-target' => '#invoice_image_path'    //此处对应Modal组件中设置的id
                        ]);
                    },
            ],
            'create_name',
            'create_time',
            'pay_confirm_name',
            'pay_confirm_time',
        ],
    ]) ?>

</div>
<?php

Modal::begin([
    'id' => 'pay_image_path',
    'header' => '<h5>支付凭证</h5>',
]);
?>
<?php echo Html::img($model->pay_image_path); ?>
<?php
Modal::end();

?>
<?php

Modal::begin([
    'id' => 'invoice_image_path',
    'header' => '<h5>发票</h5>',
]);
?>
<?php echo Html::img($model->invoice_image_path); ?>
<?php
Modal::end();

?>

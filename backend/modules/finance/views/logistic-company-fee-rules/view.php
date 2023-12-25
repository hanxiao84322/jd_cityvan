<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRules $model */

$this->title = '快递公司运费详情';
$this->params['breadcrumbs'][] = ['label' => '快递公司运费列表', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="logistic-company-fee-rules-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'warehouse_code',
            [
                'label' => '快递公司',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompany::getNameById($model->logistic_id);
                    },
            ],
            'province',
            'city',
            'district',
            'weight',
            [
                'label' => '首重取整规则',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanyFeeRules::getWeightRoundRule($model->weight_round_rule);
                    },
            ],
            'price',
            [
                'format' => 'raw',
                'label' => '续重规则',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanyFeeRules::getContinueWeightRoundRuleView($model->continue_weight_rule);
                    },
            ],
            [
                'label' => '续重取整规则',
                'value' =>
                    function ($model) {
                        return \common\models\LogisticCompanyFeeRules::getWeightRoundRule($model->continue_weight_round_rule);
                    },
            ],
            'create_username',
            'create_time',
            'update_username',
            'update_time',
        ],
    ]) ?>

</div>

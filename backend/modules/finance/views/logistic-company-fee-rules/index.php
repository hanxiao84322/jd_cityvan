<?php

use common\models\LogisticCompanyFeeRules;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyFeeRulesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '快递公司运费列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-fee-rules-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'options' => ['class' => 'hidden']//关闭分页
        ],
        'columns' => [
            'warehouse_code',
            'logistic_company_name',
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
                'label' => '快递公司',
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
            //'update_username',
            //'update_time',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view}  {update}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                    },
                    'update' => function($url, $model) {
                        return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                    },

                ]
            ],
        ],
    ]); ?>

    <?= \common\widgets\LinkPager::widget([
        'pagination' => $pages,
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'go' => true,
        'totalCount' => isset($pages->totalCount) ? $pages->totalCount : 0
    ]);
    ?>
        </div>
    </div>
</div>

<?php

use common\models\CustomerServiceDailyEfficiency;
use common\models\WorkOrderSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\WorkOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $totalNum */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '工单滞留报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-service-daily-efficiency-index">
    <div class="box">
        <div class="box-body">
            <?php echo $this->render('retention_search', ['model' => $searchModel, 'totalNum' => $totalNum]); ?>
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
                    [
                        'header' => '账户名称',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->username;
//                            return \yii\helpers\Html::a($model->transport_be_time_out, 'transport-warning-items?type=1', ['target' => '_blank']);
                        }
                    ],
                    [
                        'header' => '账户属性',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \backend\models\UserBackend::getTypeName($model->type);
                        }
                    ],
                    [
                        'header' => '客服姓名',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->name;
                        }
                    ],
                    [
                        'header' => '未完成总量',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->type == \backend\models\UserBackend::TYPE_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->not_finished_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[assign_username]=' . $model->username, ['target' => '_blank']);
                            } elseif ($model->type == \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->not_finished_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[operate_username]=' . $model->username, ['target' => '_blank']);
                            }
                        }
                    ],
                    [
                        'header' => '其中系统创建',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->type == \backend\models\UserBackend::TYPE_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->system_create_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[assign_username]=' . $model->username . '&WorkOrderSearch[system_create]=1', ['target' => '_blank']);
                            } elseif ($model->type == \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->system_create_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[operate_username]=' . $model->username . '&WorkOrderSearch[system_create]=1', ['target' => '_blank']);
                            }
                        }
                    ],
                    [
                        'header' => '其中个人创建',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->type == \backend\models\UserBackend::TYPE_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->ordinary_create_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[assign_username]=' . $model->username . '&WorkOrderSearch[ordinary_create]=1', ['target' => '_blank']);
                            } elseif ($model->type == \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->ordinary_create_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[operate_username]=' . $model->username . '&WorkOrderSearch[ordinary_create]=1', ['target' => '_blank']);
                            }
                        }
                    ],
                    [
                        'header' => '其中京东创建',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->type == \backend\models\UserBackend::TYPE_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->system_create_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[assign_username]=' . $model->username . '&WorkOrderSearch[jd_create_num]=1', ['target' => '_blank']);
                            } elseif ($model->type == \backend\models\UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                                return \yii\helpers\Html::a($model->system_create_num, '/workOrder/work-order/index?WorkOrderSearch[is_not_finished]=1&WorkOrderSearch[operate_username]=' . $model->username . '&WorkOrderSearch[jd_create_num]=1', ['target' => '_blank']);
                            }
                        }
                    ],
                ],
            ]); ?>
        </div>
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


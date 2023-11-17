<?php

use common\models\DeliveryOrderTask;
use yii\helpers\Html;
use yii\grid\GridView;

use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/** @var common\models\DeliveryOrderTaskSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */


$this->title = '任务列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <div class="box">
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body" style="overflow-x:scroll;width:1070px;white-space:nowrap;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'options' => ['class' => 'hidden']//关闭分页
                ],
                'columns' => [
                    [
                        'header' => '状态',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrderTask::getStatusName($model->status);
                        }
                    ],
                    [
                        'header' => '执行结果',
                        'headerOptions' => [
                            'style' => 'text-align:center;'
                        ],
                        'contentOptions' => ['style' => ['vertical-align' => 'middle', 'text-align' => 'center']],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return DeliveryOrderTask::getResult($model->result);
                        }
                    ],
                    'apply_username',
                    'apply_time',
                    'start_time',
                    'end_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view} {update}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('重跑', '#', ['onclick' => 'return reRun(' . $model->id . ');', 'style' => 'margin-left:15px','data-toggle' => 'modal',
            'data-target' => '#page-modal']);
                            },
                        ]
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

<?php

Modal::begin([
    'id' => 'page-modal',
    'header' => '<h5>重跑</h5>',
]);
?>
<p id="show_message"></p>
<p><?= Html::button('关闭', ['class' => 'btn btn-close', 'id' => 'close-btn']) ?></p>

<?php
Modal::end();

?>
<script>
    <?php $this->beginBlock('js') ?>
    $(function () {
        $('#close-btn').click(function () {
            location.reload(true);
        });
    });

    function reRun(id) {
        $.post({
            url: '/delivery/delivery-order-task/ajax-re-run',
            cache: false,
            dataType: 'json',
            data: {id: id},
            success: function (result) {
                if (result.status == 0) {
                    $('#page-modal').show();
                    $('#show_message').html('任务状态更新失败，原因' + result.msg);
                } else {
                    $('#page-modal').show();
                    $('#show_message').html('任务状态已更新为待执行，请稍后回来查看结果。');
                }
            }
        });
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>


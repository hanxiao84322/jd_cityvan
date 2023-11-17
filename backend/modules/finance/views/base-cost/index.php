<?php

use common\models\BaseCost;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/** @var common\models\BaseCostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '基础成本列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="base-cost-index">


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
                    [
                        'header' => '集货仓',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return BaseCost::getWarehouseName($model->warehouse);
                        }
                    ],
                    'month',
                    'data_service_fee',
                    'month_rent',
                    'worker_num',
                    'worker_fee',
                    'device_fee',
//            'create_name',
//            'create_time',
                    'update_name',
                    'update_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update} {copy}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                            },
                            'copy' => function ($url, $model) {
                                return Html::a('复制新增', '#', ['onclick' => 'return reRun(' . $model->id . ');', 'style' => 'margin-left:15px', 'data-toggle' => 'modal',
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
    'header' => '<h5>复制新增</h5>',
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
            url: 'ajax-copy',
            cache: false,
            dataType: 'json',
            type: 'get',
            data: {id: id},
            success: function (result) {
                if (result.status == 0) {
                    $('#page-modal').show();
                    $('#show_message').html(result.msg);
                } else {
                    $('#page-modal').show();
                    $('#show_message').html('复制新增成功。');
                }
            }
        });
    }
    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>


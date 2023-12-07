<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\LogisticCompanyTimelinessSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/* @var $pages yii\data\ActiveDataProvider */

$this->title = '快递公司时效列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistic-company-timeliness-index">

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
                    'timeliness',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}  {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('查看', 'view?id=' . $model->id, ['target' => '_blank']);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('修改', 'update?id=' . $model->id, ['target' => '_blank']);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('删除', '#', ['onclick' => 'return delete_by_id(' . $model->id . ');']);
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
<script>
    <?php $this->beginBlock('js') ?>
    function delete_by_id(id) {
        $.ajax({
            type: "get",
            url: "/institution/logistic-company-timeliness/ajax-delete",
            cache: false,
            data: {id: id},
            dataType: 'json',
            success: function (result) {
                console.log(result);
                if (result.status == 1) {
                    alert('删除成功！');
                } else {
                    alert(result.errorMsg);
                }
                location.reload(true);
            }
        });
    }


    <?php $this->endBlock() ?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

<?php

namespace backend\modules\workOrder\controllers;

use common\components\Utility;
use common\models\DeliveryOrder;
use common\models\WorkOrder;
use common\models\WorkOrderSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WorkOrderController implements the CRUD actions for WorkOrder model.
 */
class WorkOrderController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all WorkOrder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new WorkOrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Displays a single WorkOrder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new WorkOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        try {
            $model = new WorkOrder();

            $orderNo = \Yii::$app->request->get('order_no');
            $order = DeliveryOrder::find()->where(['order_no' => $orderNo])->asArray()->one();
            if (empty($order)) {
                throw new \Exception('订单不存在！');
            }
            $model->order_no = $orderNo;

            $model->receive_name = $order['receiver_name'];
            $model->receive_phone = $order['receiver_phone'];
            $model->receive_address = $order['receiver_address'];
            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);
                $model->work_order_no = WorkOrder::generateId();
                $model->status = WorkOrder::STATUS_WAIT_DEAL;
                $model->create_username = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '新建工单成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'orderNo' => $orderNo,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新建工单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    public function actionDeal($id)
    {
        try {
            $model = $this->findModel($id);
            if ($this->request->isPost) {
                $currentTime =  date('Y-m-d H:i:s', time());
                $post = $this->request->post();
                $model->load($post);
                $model->status = WorkOrder::STATUS_DEALT;
                $model->update_username = \Yii::$app->user->getIdentity()['username'];
                $model->update_time = $currentTime;
                $model->finished_time = $currentTime;
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '处理工单成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('deal', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '处理工单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing WorkOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WorkOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public
    function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the WorkOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return WorkOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = WorkOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

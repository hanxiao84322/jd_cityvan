<?php

namespace backend\modules\finance\controllers;

use backend\models\Institution;
use common\components\Utility;
use common\models\CustomerSettlementOrder;
use common\models\CustomerSettlementOrderDetail;
use common\models\CustomerSettlementOrderDetailSearch;
use common\models\CustomerSettlementOrderSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerSettlementOrderController implements the CRUD actions for CustomerSettlementOrder model.
 */
class CustomerSettlementOrderController extends Controller
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
     * Lists all CustomerSettlementOrder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $params = $this->request->queryParams;
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $params['CustomerSettlementOrderSearch']['institution_id'] = $institutionId;
        $searchModel = new CustomerSettlementOrderSearch();
        $dataProvider = $searchModel->search($params);
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
            'institutionId' => $institutionId,
            'level' => $level,
        ]);
    }

    /**
     * Displays a single CustomerSettlementOrder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new CustomerSettlementOrderDetailSearch();
        $params['CustomerSettlementOrderDetailSearch']['institution_id'] = $model->institution_id;
        $params['CustomerSettlementOrderDetailSearch']['customer_id'] = $model->customer_id;
        $params['CustomerSettlementOrderDetailSearch']['settlement_order_no'] = $model->settlement_order_no;
        $dataProvider = $searchModel->search($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('view', [
            'settlement_order_no' => $searchModel->settlement_order_no,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);

    }

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $searchModel = new CustomerSettlementOrderDetailSearch();
        $params['CustomerSettlementOrderDetailSearch']['institution_id'] = $model->institution_id;
        $params['CustomerSettlementOrderDetailSearch']['customer_id'] = $model->customer_id;
        $dataProvider = $searchModel->searchPrint($params);

        return $this->render('print', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Creates a new CustomerSettlementOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $model = new CustomerSettlementOrder();
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                $detailBatchAddRes = CustomerSettlementOrderDetail::batchAdd($institutionId, $post['CustomerSettlementOrder']['customer_id'], $post['CustomerSettlementOrder']['start_time'], $post['CustomerSettlementOrder']['end_time']);
                if (!$detailBatchAddRes['success']) {
                    throw new \Exception(Utility::arrayToString($detailBatchAddRes['errorList']));
                }
                $orderAddRes = CustomerSettlementOrder::add($institutionId, $post['CustomerSettlementOrder']['customer_id'], $post['CustomerSettlementOrder']['start_time'], $post['CustomerSettlementOrder']['end_time']);
                if (!$orderAddRes['success']) {
                    throw new \Exception(Utility::arrayToString($orderAddRes['errorList']));
                }
                \Yii::$app->session->setFlash('success', '新建结算单成功!');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'institutionId' => $institutionId,
                    'level' => $level,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新建结算单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing CustomerSettlementOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCollection($id)
    {
        $model = $this->findModel($id);
        try {
            if ($this->request->isPost) {
                $file = $_FILES;
                if (empty($file['CustomerSettlementOrder']['name']['pay_image_path'])) {
                    throw new \Exception('支付凭证图片不能为空');
                }
                if ($file['CustomerSettlementOrder']['type']['pay_image_path'] != 'image/jpeg') {
                    throw new \Exception('支付凭证图片格式只支持jpg或jpeg');
                }
                if ($file['CustomerSettlementOrder']['size']['pay_image_path'] > 2097152) {
                    throw new \Exception('支付凭证图片不能超过2MB');
                }
                $path = 'uploads/pay_image/' . date('Y-m-d', time()) . '/';
                $filePath = $path . 'SO' . time() . '.jpg';
                //创建目录失败
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }
                if (!move_uploaded_file($file['CustomerSettlementOrder']['tmp_name']['pay_image_path'], $filePath)) {
                    throw new \Exception('复制文件失敗');
                }
                $model->status = CustomerSettlementOrder::STATUS_PAID;
                $model->pay_image_path = '/' . $filePath;
                $model->update_name = \Yii::$app->user->getIdentity()['username'];
                $model->update_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            return $this->render('collection', [
                'model' => $model,
            ]);
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '结算单确认收款，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }


    /**
     * Deletes an existing CustomerSettlementOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CustomerSettlementOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CustomerSettlementOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerSettlementOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

namespace backend\modules\delivery\controllers;

use backend\models\OrderFiles;
use backend\models\OrderFilesSearch;
use common\components\Utility;
use common\models\DeliveryAdjustOrder;
use common\models\DeliveryAdjustOrderSearch;
use common\models\DeliveryOrder;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeliveryAdjustOrderController implements the CRUD actions for DeliveryAdjustOrder model.
 */
class DeliveryAdjustOrderController extends Controller
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
     * Lists all DeliveryAdjustOrder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DeliveryAdjustOrderSearch();
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
     * Displays a single DeliveryAdjustOrder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $orderFilesSearchModel = new OrderFilesSearch();
        $orderFilesSearchParams['OrderFilesSearch']['order_id'] = $model->id;
        $orderFilesSearchParams['OrderFilesSearch']['type'] = OrderFiles::TYPE_DELIVERY_ADJUST_ORDER;
        $orderFilesSearchDataProvider = $orderFilesSearchModel->search($orderFilesSearchParams);
        return $this->render('view', [
            'model' => $model,
            'orderFilesSearchDataProvider' => $orderFilesSearchDataProvider
        ]);
    }

    /**
     * Creates a new DeliveryAdjustOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        try {
            $logisticNo = \Yii::$app->request->get('logistic_no');
            $order = DeliveryOrder::find()->where(['logistic_no' => $logisticNo])->asArray()->one();
            if (empty($order)) {
                throw new \Exception('订单不存在！');
            }

            $model = new DeliveryAdjustOrder();

            $model->logistic_no = $logisticNo;
            $model->adjust_order_no = DeliveryAdjustOrder::generateId();

            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);
                $deliveryAdjustOrderExists = DeliveryAdjustOrder::find()->where(['logistic_no' => $logisticNo, 'type' => $model->type])->exists();
                if ($deliveryAdjustOrderExists) {
                    throw new \Exception('该订单已经创建了相同类型的调整单！');
                }
                $file = $_FILES;
                $path = 'uploads/order_files/delivery_adjust_order/' . date('Y-m-d', time()) . '/';
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }
                $orderFilesIds = [];
                if (!empty($file['DeliveryAdjustOrder']['name']['files']) && !empty($file['DeliveryAdjustOrder']['tmp_name']['files'])) {
                    foreach ($file['DeliveryAdjustOrder']['name']['files'] as $key => $value) {
                        $filePath = '';
                        $tmp = explode('.', $file['DeliveryAdjustOrder']['name']['files'][$key]);
                        $suffix = array_pop($tmp);

                        if (!in_array($suffix, ['mp4', 'jpg', 'jpeg', 'mp3'])) {
                            throw new \Exception('附件格式只支持jpg、mp3、mp4格式');
                        }
                        if ($file['DeliveryAdjustOrder']['size']['files'][$key] > 2097152) {
                            throw new \Exception('附件不能超过2MB');
                        }
                        $fileName = $file['DeliveryAdjustOrder']['name']['files'][$key];
                        $filePath = $path . $fileName;

                        if (!move_uploaded_file($file['DeliveryAdjustOrder']['tmp_name']['files'][$key], $filePath)) {
                            throw new \Exception('复制文件失敗');
                        }
                        $orderFilesModel = new OrderFiles();
                        $orderFilesModel->type = OrderFiles::TYPE_DELIVERY_ADJUST_ORDER;
                        $orderFilesModel->file_path = $filePath;
                        $orderFilesModel->order_id = 0;
                        $orderFilesModel->name = $fileName;
                        $orderFilesModel->create_username = \Yii::$app->user->getIdentity()['username'];
                        $orderFilesModel->create_time = date('Y-m-d H:i:s', time());
                        if (!$orderFilesModel->save()) {
                            throw new \Exception('保存附件失败：' . Utility::arrayToString($orderFilesModel->getErrors()));
                        }
                        $orderFilesIds[] = \Yii::$app->db->lastInsertID;
                    }
                }


                $model->create_name = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    OrderFiles::updateAll(['order_id' => \Yii::$app->db->lastInsertID], ['id' => $orderFilesIds]);
                    \Yii::$app->session->setFlash('success', '新建订单调整单成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新建订单调整单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing DeliveryAdjustOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        try {
            $model = $this->findModel($id);

            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);

                $file = $_FILES;
                $path = 'uploads/order_files/delivery_adjust_order/' . date('Y-m-d', time()) . '/';
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }
                $orderFilesIds = [];
                if (!empty($file['DeliveryAdjustOrder']['name']['files']) && !empty($file['DeliveryAdjustOrder']['tmp_name']['files'])) {
                    foreach ($file['DeliveryAdjustOrder']['name']['files'] as $key => $value) {
                        $filePath = '';
                        $tmp = explode('.', $file['DeliveryAdjustOrder']['name']['files'][$key]);
                        $suffix = array_pop($tmp);

                        if (!in_array($suffix, ['mp4', 'jpg', 'jpeg', 'mp3'])) {
                            throw new \Exception('附件格式只支持jpg、mp3、mp4格式');
                        }
                        if ($file['DeliveryAdjustOrder']['size']['files'][$key] > 2097152) {
                            throw new \Exception('附件不能超过2MB');
                        }
                        $fileName = $file['DeliveryAdjustOrder']['name']['files'][$key];
                        $filePath = $path . $fileName;

                        if (!move_uploaded_file($file['DeliveryAdjustOrder']['tmp_name']['files'][$key], $filePath)) {
                            throw new \Exception('复制文件失敗');
                        }
                        $orderFilesModel = new OrderFiles();
                        $orderFilesModel->type = OrderFiles::TYPE_DELIVERY_ADJUST_ORDER;
                        $orderFilesModel->file_path = $filePath;
                        $orderFilesModel->order_id = $model->id;
                        $orderFilesModel->name = $fileName;
                        $orderFilesModel->create_username = \Yii::$app->user->getIdentity()['username'];
                        $orderFilesModel->create_time = date('Y-m-d H:i:s', time());
                        if (!$orderFilesModel->save()) {
                            throw new \Exception('保存附件失败：' . Utility::arrayToString($orderFilesModel->getErrors()));
                        }
                        $orderFilesIds[] = \Yii::$app->db->lastInsertID;
                    }
                }

                $model->update_name  = \Yii::$app->user->getIdentity()['username'];
                $model->update_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '修改订单调整单成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '修改订单调整单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }


    }

    /**
     * Deletes an existing DeliveryAdjustOrder model.
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
     * Finds the DeliveryAdjustOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DeliveryAdjustOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeliveryAdjustOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * 工单附件下载
     * @param $id
     */
    public function actionDownloadOrderFile($id)
    {
        $orderFilesModel = OrderFiles::findOne($id);
        $filePath = '/www/wwwroot/jd_cityvan/backend/web/' . $orderFilesModel->file_path;
        //$filePath = '/Users/hanxiao/code/jd_cityvan/backend/web/' . $orderFilesModel->file_path;
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($filePath));
        header("Content-Disposition: attachment; filename=" . $orderFilesModel->name);
        readfile($filePath);
        exit;
    }


    public function actionDeleteOrderFile($id)
    {
        $orderFileModel = OrderFiles::findOne($id);
        $orderId = $orderFileModel->order_id;
        $orderFileModel->delete();
        \Yii::$app->session->setFlash('success', '删除附件成功!');
        return $this->redirect(['view', 'id' => $orderId]);
    }

    public function actionApprove()
    {
        
    }

}

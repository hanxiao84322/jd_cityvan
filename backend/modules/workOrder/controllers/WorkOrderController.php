<?php

namespace backend\modules\workOrder\controllers;

use backend\models\OrderFiles;
use backend\models\OrderFilesSearch;
use backend\models\UserBackend;
use common\components\Utility;
use common\models\DeliveryOrder;
use common\models\ImportantCustomer;
use common\models\LogisticCompany;
use common\models\WorkOrder;
use common\models\WorkOrderReply;
use common\models\WorkOrderReplySearch;
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
        $params = $this->request->queryParams;
        $userType = \Yii::$app->user->getIdentity()['type'];
        $dataPower = [];
        if ($userType == UserBackend::TYPE_CUSTOMER_SERVICE) {
            $params['WorkOrderSearch']['create_username'] = \Yii::$app->user->getIdentity()['username'];
        } elseif ($userType == UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
            $dataPower['logisticIds'] = \Yii::$app->user->getIdentity()['logistic_id_list'];
        } else {
            $dataPower = [];
        }
        $searchModel = new WorkOrderSearch();
        if (!isset($params['WorkOrderSearch']['status']) || $params['WorkOrderSearch']['status'] != WorkOrder::STATUS_FINISHED) {
            $params['WorkOrderSearch']['is_not_finished'] = 1;
        }
        $dataProvider = $searchModel->search($params, $dataPower);
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
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        $id = \Yii::$app->request->get('id');
        $logisticNo = \Yii::$app->request->get('logistic_no');
        if (empty($id)) {
            $id = WorkOrder::find()->select('id')->where(['logistic_no' => $logisticNo]);
        }
        $model = $this->findModel($id);
        $searchModel = new WorkOrderReplySearch();
        $params['WorkOrderReplySearch']['work_order_no'] = $model->work_order_no;
        $dataProvider = $searchModel->search($params);
        $orderFilesSearchModel = new OrderFilesSearch();
        $orderFilesSearchParams['OrderFilesSearch']['order_id'] = $model->id;
        $orderFilesSearchParams['OrderFilesSearch']['type'] = OrderFiles::TYPE_WORK_ORDER;
        $orderFilesSearchDataProvider = $orderFilesSearchModel->search($orderFilesSearchParams);
        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'orderFilesSearchDataProvider' => $orderFilesSearchDataProvider
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

            $logisticNo = \Yii::$app->request->get('logistic_no');
            $order = DeliveryOrder::find()->where(['logistic_no' => $logisticNo])->asArray()->one();
            if (empty($order)) {
                throw new \Exception('订单不存在！');
            }
            $model->order_no = $order['order_no'];
            $model->logistic_no = $logisticNo;
            $model->warehouse_code = $order['warehouse_code'];

            $model->receive_name = $order['receiver_name'];
            $model->receive_phone = $order['receiver_phone'];
            $model->receive_address = $order['receiver_address'];
            $model->logistic_id = $order['logistic_id'];
            $model->order_create_num = WorkOrder::getCreateNumByLogisticNo($order['logistic_no']);
            $model->customer_attention_level = ImportantCustomer::getLevelByNameAndPhone($order['receiver_name'], $order['receiver_phone']);
            $model->logistic_company = LogisticCompany::getNameById($order['logistic_id']);
            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);

                $file = $_FILES;
                $path = 'uploads/order_files/work_order/' . date('Y-m-d', time()) . '/';
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }
                $orderFilesIds = [];
                if (!empty($file['WorkOrder']['name']['files'][0]) && !empty($file['WorkOrder']['tmp_name']['files'][0])) {
                    foreach ($file['WorkOrder']['name']['files'] as $key => $value) {
                        $filePath = '';
                        $tmp = explode('.', $file['WorkOrder']['name']['files'][$key]);
                        $suffix = array_pop($tmp);

                        if (!in_array($suffix, ['mp4', 'wav', 'jpg', 'jpeg', 'mp3'])) {
                            throw new \Exception('附件格式只支持jpg、mp3、mp4、wav格式');
                        }
                        if ($file['WorkOrder']['size']['files'][$key] > 2097152) {
                            throw new \Exception('附件不能超过2MB');
                        }
                        $fileName = $file['WorkOrder']['name']['files'][$key];
                        $filePath = $path . $fileName;

                        if (!move_uploaded_file($file['WorkOrder']['tmp_name']['files'][$key], $filePath)) {
                            throw new \Exception('复制文件失敗');
                        }
                        $orderFilesModel = new OrderFiles();
                        $orderFilesModel->type = OrderFiles::TYPE_WORK_ORDER;
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

                if (!empty($model->jd_work_order_no)) {
                    $model->jd_create = 1;
                } else {
                    $model->ordinary_create = 1;
                }
                $model->work_order_no = WorkOrder::generateId();
                $model->file_path = '/';
                $model->status = WorkOrder::STATUS_WAIT_ALLOCATION;
                $model->assign_username = \Yii::$app->user->getIdentity()['username'];
                $model->create_username = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    OrderFiles::updateAll(['order_id' => \Yii::$app->db->lastInsertID], ['id' => $orderFilesIds]);
                    \Yii::$app->session->setFlash('success', '新建工单成功!');
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
            \Yii::$app->session->setFlash('error', '新建工单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['/delivery/delivery-order/index']);
        }
    }

    public function actionDeal()
    {
        try {
            $id = \Yii::$app->request->get('id');
            $logisticNo = \Yii::$app->request->get('logistic_no');
            if (empty($id)) {
                $id = WorkOrder::find()->select('id')->where(['logistic_no' => $logisticNo]);
            }
            $model = $this->findModel($id);

            $userType = \Yii::$app->user->getIdentity()['type'];
//            if ($userType == UserBackend::TYPE_CUSTOMER_SERVICE) {
//                if (\Yii::$app->user->getIdentity()['username'] != $model->assign_username) {
//                    throw new \Exception('只有指派人可以处理工单');
//                }
//            }
            if ($userType == UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
                if (!empty($model->operate_username)) {
                    if (\Yii::$app->user->getIdentity()['username'] != $model->operate_username) {
                        throw new \Exception('只有负责人可以处理工单');
                    }
                }
            }

            $searchModel = new WorkOrderReplySearch();
            $params['WorkOrderReplySearch']['work_order_no'] = $model->work_order_no;
            $dataProvider = $searchModel->search($params);
            $orderFilesSearchModel = new OrderFilesSearch();
            $orderFilesSearchParams['OrderFilesSearch']['order_id'] = $model->id;
            $orderFilesSearchParams['OrderFilesSearch']['type'] = OrderFiles::TYPE_WORK_ORDER;
            $orderFilesSearchDataProvider = $orderFilesSearchModel->search($orderFilesSearchParams);
            if ($this->request->isPost) {
                $post = $this->request->post();

                $file = $_FILES;
                $path = 'uploads/order_files/work_order/' . date('Y-m-d', time()) . '/';
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }

                if (!empty($file['WorkOrder']['name']['files'][0]) && !empty($file['WorkOrder']['tmp_name']['files'][0])) {
                    foreach ($file['WorkOrder']['name']['files'] as $key => $value) {
                        $filePath = '';
                        $tmp = explode('.', $file['WorkOrder']['name']['files'][$key]);
                        $suffix = array_pop($tmp);

                        if (!in_array($suffix, ['mp4', 'wav', 'jpg', 'jpeg', 'mp3'])) {
                            throw new \Exception('附件格式只支持jpg、mp3、mp4、wav格式');
                        }
                        if ($file['WorkOrder']['size']['files'][$key] > 2097152) {
                            throw new \Exception('附件不能超过2MB');
                        }
                        $fileName = $file['WorkOrder']['name']['files'][$key];
                        $filePath = $path . $fileName;

                        if (!move_uploaded_file($file['WorkOrder']['tmp_name']['files'][$key], $filePath)) {
                            throw new \Exception('复制文件失敗');
                        }
                        $orderFilesModel = new OrderFiles();
                        $orderFilesModel->type = OrderFiles::TYPE_WORK_ORDER;
                        $orderFilesModel->file_path = $filePath;
                        $orderFilesModel->order_id = $model->id;
                        $orderFilesModel->name = $fileName;
                        $orderFilesModel->create_username = \Yii::$app->user->getIdentity()['username'];
                        $orderFilesModel->create_time = date('Y-m-d H:i:s', time());
                        if (!$orderFilesModel->save()) {
                            throw new \Exception('保存附件失败：' . Utility::arrayToString($orderFilesModel->getErrors()));
                        }
                    }
                }
                $status = $model->status;
                if (isset($post['pending_submit'])) {
                    $status = WorkOrder::STATUS_PENDING;
                } elseif (isset($post['deal_finish_submit'])) {
                    $status = WorkOrder::STATUS_DEALT;
                } elseif (isset($post['finished_submit'])) {
                    $status = WorkOrder::STATUS_FINISHED;
                } elseif (isset($post['deal_submit'])) {
                    $status = WorkOrder::STATUS_WAIT_DEAL;
                }
                $workOrderReplyModel = new WorkOrderReply();
                $workOrderReplyModel->work_order_no = $model->work_order_no;
                $workOrderReplyModel->reply_content = $post['reply_content'];
                $workOrderReplyModel->status = $status;
                $workOrderReplyModel->reply_name = \Yii::$app->user->getIdentity()['username'];
                $workOrderReplyModel->reply_time = date('Y-m-d H:i:s', time());
                if ($workOrderReplyModel->save()) {
                    if ($status == WorkOrder::STATUS_FINISHED) {
                        $model->finished_time = date('Y-m-d H:i:s', time());
                    }
                    $model->status = $status;
                    if ($userType == UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE && empty($model->operate_username)) {
                        $model->operate_username = \Yii::$app->user->getIdentity()['username'];
                    }
                    $model->latest_reply = $post['reply_content'];
                    $model->update_username = \Yii::$app->user->getIdentity()['username'];
                    $model->update_time = date('Y-m-d H:i:s', time());
                    if (!$model->save()) {
                        throw new \Exception(Utility::arrayToString($model->getErrors()));
                    }

                    \Yii::$app->session->setFlash('success', '处理工单成功!');
                    return $this->redirect(['deal', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($workOrderReplyModel->getErrors()));
                }
            } else {
                return $this->render('deal', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'orderFilesSearchDataProvider' => $orderFilesSearchDataProvider
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
        try {
            $model = $this->findModel($id);
            if (\Yii::$app->user->getIdentity()['username'] != $model->assign_username) {
                throw new \Exception('只有指派人可以修改工单');
            }
            $model->logistic_company = LogisticCompany::getNameById($model->logistic_id);
            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);
                $file = $_FILES;
                $path = 'uploads/order_files/work_order/' . date('Y-m-d', time()) . '/';
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }
                if (!empty($file['WorkOrder']['name']['files'][0]) && !empty($file['WorkOrder']['tmp_name']['files'][0])) {
                    foreach ($file['WorkOrder']['name']['files'] as $key => $value) {
                        $filePath = '';
                        $tmp = explode('.', $file['WorkOrder']['name']['files'][$key]);
                        $suffix = array_pop($tmp);

                        if (!in_array($suffix, ['mp4', 'wav', 'jpg', 'jpeg', 'mp3'])) {
                            throw new \Exception('附件格式只支持jpg、mp3、mp4、wav格式');
                        }
                        if ($file['WorkOrder']['size']['files'][$key] > 2097152) {
                            throw new \Exception('附件不能超过2MB');
                        }
                        $fileName = $file['WorkOrder']['name']['files'][$key];
                        $filePath = $path . $fileName;

                        if (!move_uploaded_file($file['WorkOrder']['tmp_name']['files'][$key], $filePath)) {
                            throw new \Exception('复制文件失敗');
                        }
                        $orderFilesModel = new OrderFiles();
                        $orderFilesModel->type = OrderFiles::TYPE_WORK_ORDER;
                        $orderFilesModel->file_path = $filePath;
                        $orderFilesModel->order_id = $id;
                        $orderFilesModel->name = $fileName;
                        $orderFilesModel->create_username = \Yii::$app->user->getIdentity()['username'];
                        $orderFilesModel->create_time = date('Y-m-d H:i:s', time());
                        if (!$orderFilesModel->save()) {
                            throw new \Exception('保存附件失败：' . Utility::arrayToString($orderFilesModel->getErrors()));
                        }
                    }
                }
                if (!empty($model->jd_work_order_no)) {
                    $model->jd_create = 1;
                } else {
                    $model->ordinary_create = 1;
                }
                $model->file_path = '/';
                $model->update_username = \Yii::$app->user->getIdentity()['username'];
                $model->update_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '修改工单成功!');
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
            \Yii::$app->session->setFlash('error', '修改工单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['/delivery/delivery-order/index']);
        }
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

    /**
     * 工单附件下载
     * @param $id
     */
    public function actionDownloadOrderFile($id)
    {
        $orderFilesModel = OrderFiles::findOne($id);
        $filePath = '/www/wwwroot/jd_cityvan/backend/web/' . $orderFilesModel->file_path;
//        $filePath = '/Users/hanxiao/code/jd_cityvan/backend/web/' . $orderFilesModel->file_path;
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($filePath));
        header("Content-Disposition: attachment; filename=" . $orderFilesModel->name);
        readfile($filePath);
        exit;
    }


    /**
     * Lists all DeliveryOrder models.
     *
     * @return string
     */
    public function actionExportData()
    {
        $userType = \Yii::$app->user->getIdentity()['type'];
        if ($userType == UserBackend::TYPE_CUSTOMER_SERVICE) {
            $dataPower['warehouseCodes'] = \Yii::$app->user->getIdentity()['warehouse_code_list'];
        } elseif ($userType == UserBackend::TYPE_LOGISTIC_CUSTOMER_SERVICE) {
            $dataPower['logisticIds'] = \Yii::$app->user->getIdentity()['logistic_id_list'];
        } else {
            $dataPower = [];
        }
        $params = $this->request->queryParams;
        $searchModel = new WorkOrderSearch();
        $searchModel->exportData($params, $dataPower);
    }


    public function actionDeleteOrderFile($id)
    {
        $orderFileModel = OrderFiles::findOne($id);
        $orderId = $orderFileModel->order_id;
        $orderFileModel->delete();
        \Yii::$app->session->setFlash('success', '删除附件成功!');
        return $this->redirect(['view', 'id' => $orderId]);
    }


}
<?php

namespace backend\modules\delivery\controllers;

use backend\models\OrderFiles;
use backend\models\OrderFilesSearch;
use common\components\Utility;
use common\models\ApproveLog;
use common\models\ApproveLogSearch;
use common\models\DeliveryAdjustOrder;
use common\models\DeliveryAdjustOrderSearch;
use common\models\DeliveryOrder;
use Psy\Util\Json;
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

        $approveLogSearch = new ApproveLogSearch();
        $approveLogSearchParams['ApproveLogSearch']['order_id'] = $model->id;
        $approveLogSearchParams['ApproveLogSearch']['type'] = ApproveLog::ORDER_TYPE_DELIVERY_ADJUST;
        $approveLogProvider = $approveLogSearch->search($approveLogSearchParams);
        return $this->render('view', [
            'model' => $model,
            'orderFilesSearchDataProvider' => $orderFilesSearchDataProvider,
            'approveLogProvider' => $approveLogProvider
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

                $orderFilesIds = [];
                if (!empty($file['DeliveryAdjustOrder']['name']['files'][0]) && !empty($file['DeliveryAdjustOrder']['tmp_name']['files'][0])) {

                    $path = 'uploads/order_files/delivery_adjust_order/' . date('Y-m-d', time()) . '/';
                    if (!file_exists($path) && !mkdir($path, 0777, true)) {
                        throw new \Exception('创建文件夹失敗！');
                    } else if (!is_writeable($path)) {
                        throw new \Exception('文件夹不可写！');
                    }
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
                if (!empty($file['DeliveryAdjustOrder']['name']['files'][0]) && !empty($file['DeliveryAdjustOrder']['tmp_name']['files'][0])) {
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

    public function actionAjaxFirstApprove()
    {
        $return = [
            'status' => 0,
            'errMsg' => '',
        ];

        try {
            $post = \Yii::$app->request->post();

            $deliveryAdjustId = $post['delivery_adjust_id'];
            $opinion = $post['opinion'];
            $type = $post['type'];

            $model = $this->findModel($deliveryAdjustId);
            if ($type == 'approve') {
                if (!in_array($model->status, [DeliveryAdjustOrder::STATUS_CREATE, DeliveryAdjustOrder::STATUS_FIRST_REJECTED])) {
                    throw new \Exception('只有新建和一级审核驳回状态可以操作一级审核！');
                }
            } else {
                if (!in_array($model->status, [DeliveryAdjustOrder::STATUS_CREATE])) {
                    throw new \Exception('只有新建状态可以操作一级驳回！');
                }
                if (empty($opinion)) {
                    throw new \Exception('驳回操作审核备注必须填写！');
                }
            }
            $model->status = ($type == 'approve') ? DeliveryAdjustOrder::STATUS_FIRST_APPROVED : DeliveryAdjustOrder::STATUS_FIRST_REJECTED;
            $model->first_approve_username = \Yii::$app->user->getIdentity()['username'];
            $model->first_approve_name = \Yii::$app->user->getIdentity()['name'];
            $model->first_approve_opinion = $opinion;
            $model->first_approve_time = date('Y-m-d H:i:s', time());
            if (!$model->save()) {
                throw new \Exception(Utility::arrayToString($model->getErrors()));
            } else {
                $approveModel = new ApproveLog();
                $approveModel->order_type = ApproveLog::ORDER_TYPE_DELIVERY_ADJUST;
                $approveModel->order_id = $model->id;
                $approveModel->approve_node = '一级审核(系统客服)';
                $approveModel->approve_status = ($type == 'approve') ? ApproveLog::STATUS_APPROVED : ApproveLog::STATUS_REJECTED;
                $approveModel->approve_opinion = $model->first_approve_opinion;
                $approveModel->approve_username = \Yii::$app->user->getIdentity()['username'];
                $approveModel->approve_name = \Yii::$app->user->getIdentity()['name'];
                $approveModel->approve_time = date('Y-m-d H:i:s', time());
                if (!$approveModel->save()) {
                    throw new \Exception(Utility::arrayToString($approveModel->getErrors()));
                }
            }
            $return['status'] = 1;
        } catch (\Exception $e) {
            $return['errMsg'] = $e->getMessage();
        }
        exit(Json::encode($return));
    }

    public function actionAjaxSecApprove()
    {

        $return = [
            'status' => 0,
            'errMsg' => '',
        ];

        try {
            $post = \Yii::$app->request->post();

            $deliveryAdjustId = $post['delivery_adjust_id'];
            $opinion = $post['opinion'];
            $type = $post['type'];

            $model = $this->findModel($deliveryAdjustId);

            if ($type == 'approve') {
                if (!in_array($model->status, [DeliveryAdjustOrder::STATUS_FIRST_APPROVED, DeliveryAdjustOrder::STATUS_SEC_REJECTED])) {
                    throw new \Exception('只有新建和二级审核驳回状态可以操作二级审核！');
                }
            } else {
                if (!in_array($model->status, [DeliveryAdjustOrder::STATUS_FIRST_APPROVED])) {
                    throw new \Exception('只有一级审核通过状态可以操作二级驳回！');
                }
                if (empty($opinion)) {
                    throw new \Exception('驳回操作审核备注必须填写！');
                }
            }
            $model->status = ($type == 'approve') ? DeliveryAdjustOrder::STATUS_SEC_APPROVED : DeliveryAdjustOrder::STATUS_SEC_REJECTED;
            $model->first_approve_username = \Yii::$app->user->getIdentity()['username'];
            $model->first_approve_name = \Yii::$app->user->getIdentity()['name'];
            $model->first_approve_opinion = $opinion;
            $model->first_approve_time = date('Y-m-d H:i:s', time());
            if (!$model->save()) {
                throw new \Exception(Utility::arrayToString($model->getErrors()));
            } else {
                $approveModel = new ApproveLog();
                $approveModel->order_type = ApproveLog::ORDER_TYPE_DELIVERY_ADJUST;
                $approveModel->order_id = $model->id;
                $approveModel->approve_node = '二级审核(财务)';
                $approveModel->approve_status = ($type == 'approve') ? ApproveLog::STATUS_APPROVED : ApproveLog::STATUS_REJECTED;
                $approveModel->approve_opinion = $model->first_approve_opinion;
                $approveModel->approve_username = \Yii::$app->user->getIdentity()['username'];
                $approveModel->approve_name = \Yii::$app->user->getIdentity()['name'];
                $approveModel->approve_time = date('Y-m-d H:i:s', time());
                if (!$approveModel->save()) {
                    throw new \Exception(Utility::arrayToString($approveModel->getErrors()));
                }
            }
            $return['status'] = 1;
        } catch (\Exception $e) {
            $return['errMsg'] = $e->getMessage();
            print_r($return);exit;
        }
        exit(Json::encode($return));
    }

    /**
     * Lists all DeliveryAdjustOrder models.
     *
     * @return string
     */
    public function actionWaitApprove()
    {
        $searchModel = new DeliveryAdjustOrderSearch();
        $approveLogSearchParams = $this->request->queryParams;
        $approveLogSearchParams['DeliveryAdjustOrderSearch']['status'] = '';
        $dataProvider = $searchModel->search($approveLogSearchParams);
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
}

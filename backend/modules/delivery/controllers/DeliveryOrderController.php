<?php

namespace backend\modules\delivery\controllers;

use backend\models\Institution;
use backend\models\UserBackend;
use common\components\EmsCloud;
use common\components\KdApi;
use common\components\Utility;
use common\models\DeliveryInfo;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderSearch;
use common\models\LogisticImage;
use common\models\Warehouse;
use yii\data\Pagination;
use yii\db\Exception;
use \yii\helpers\Json;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * DeliveryOrderController implements the CRUD actions for DeliveryOrder model.
 */
class DeliveryOrderController extends Controller
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
     * Lists all DeliveryOrder models.
     *
     * @return string
     */
    public function actionIndex()
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

        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->search($params, 1, $dataPower);
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
        $searchModel = new DeliveryOrderSearch();
        $searchModel->exportData($params, $dataPower);
    }

    /**
     * Displays a single DeliveryOrder model.
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
     * Creates a new DeliveryOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DeliveryOrder();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DeliveryOrder model.
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
     * Deletes an existing DeliveryOrder model.
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
     * Finds the DeliveryOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DeliveryOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeliveryOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 批量导入许可证编号模板下载
     */
    public function actionDownloadTemplate()
    {
        $excelUrl = "/www/wwwroot/jd_cityvan/backend/batch_upload_template/template1.xlsx";
//        $excelUrl = "/Users/hanxiao/code/jd_cityvan/backend/batch_upload_template/template.xlsx";
        $filename = "批量导入运单信息模板.xlsx";
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($excelUrl));
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($excelUrl);
        exit;
    }

    /**
     *
     */
    public function actionAjaxBatchUpdate()
    {
        ini_set("max_execution_time", -1);

        $return = [
            'status' => 0,
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => '',
        ];
        // 文件校验
        try {
            $file = $_FILES['file'];
            if (empty($file['tmp_name'])) {
                throw new \Exception('上传文件不能为空');
            }
            $tmp = explode('.', $file['name']);
            $suffix = array_pop($tmp);
            if ('xlsx' !== $suffix) {
                throw new \Exception('上传文件无法识别，请使用模版');
            }
            if ($file['size'] > 2097152) {
                throw new \Exception('上传文件不能超过2MB');
            }
            $excelData = Utility::getExcelDataNew($file['tmp_name']);
            if (empty($excelData)) {
                throw new \Exception('上传文件无内容');
            }
        } catch (\Exception $e) {
            $return['errorList'] = $e->getMessage();
            exit(Json::encode($return));
        }
// 导入
        if (empty($excelData)) {
            $return['errorList'] = '数据为空';
            exit(Json::encode($return));
        }
        if (count($excelData) >= 5000) {
            $return['errorList'] = '数据量太大，不能超过5000条。';
            exit(Json::encode($return));
        }
        $return = DeliveryOrder::batchUpdate($excelData, \Yii::$app->user->getIdentity()['username']);
        $return['status'] = 1;
        $return['errorList'] = Utility::arrayToString($return['errorList']);
        if (!empty($return['errorData'])) {
            $fileName = '订单导入失败数据-' . date('YmdHi');
            $header = [
                '快递单号',
                '日期',
                '库房号',
                '订单号',
                '包裹数量',
                '包裹号',
                '订单重量',
                '订单重量（复重）',
                '包裹重量',
                '包裹重量（复重）',
                '客户姓名',
                '客户地址',
                '客户电话',
                '物流重量',
                '物流公司'
            ];
            Utility::exportData($return['errorData'], $header, $fileName, $fileName);
        }

        echo Json::encode($return);
        exit;
    }

    public function actionAjaxGetDeliveryInfoSteps()
    {
        $return = [
            'status' => false,
            'msg' => '',
            'html' => ''
        ];
        $logisticNo = \Yii::$app->request->get('logistic_no', '');
        $html = DeliveryInfo::getStepsByLogisticNo($logisticNo);
        $return['status'] = true;
        $return['html'] = $html;
        exit(Json::encode($return));
    }

    /**
     * Lists all DeliveryOrder models.
     *
     * @return string
     */
    public function actionNoImage()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $params = $this->request->queryParams;
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        if ($level == Institution::LEVEL_SUN) {
            $params['DeliveryOrderSearch']['is_sun'] = 1;
        }
        if ($level != Institution::LEVEL_PARENT) {
            $params['DeliveryOrderSearch']['institution_id'] = $institutionId;
        }
        $params['DeliveryOrderSearch']['is_upload_image'] = DeliveryOrder::NOT;
        $params['DeliveryOrderSearch']['is_need_analysis_ocr'] = DeliveryOrder::YES;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->search($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('no-image', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'institutionId' => $institutionId,
            'level' => $level,
        ]);
    }

    public function actionUpload($logistic_no)
    {
        $modelExists = LogisticImage::find()->where(['logistic_no' => $logistic_no])->exists();
        if ($modelExists) {
            $model = LogisticImage::findOne(['logistic_no' => $logistic_no]);
        } else {
            $model = new LogisticImage();
        }
        try {
            if ($this->request->isPost) {
                $file = $_FILES['file'];

                if (empty($file['tmp_name'])) {
                    throw new \Exception('上传文件不能为空');
                }

                $tmp = explode('.', $file['name']);
                $suffix = array_pop($tmp);
                if ('jpg' !== $suffix && 'jpeg' !== $suffix) {
                    throw new \Exception('上传图片格式必须是jpg');
                }

                if ($file['size'] > 5097152) {
                    throw new \Exception('上传文件不能超过5MB');
                }
                $model->logistic_no = $logistic_no;
                $model->image_base64_str = Utility::base64EncodeImage($file['tmp_name']);
                if (!$model->save()) {
                    throw new \Exception($model->getErrors());
                }
                \Yii::$app->session->setFlash('success', '上传成功!');
                return $this->redirect(['no-image']);

            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '上传失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['no-image']);
        }
        return $this->render('upload', [
            'model' => $model,
        ]);
    }

    public function actionZjsDeliveryInfo()
    {
        $deliverySteps = [];
        try {
            if ($this->request->isPost) {
                $orderNo = $this->request->post()['order_no'];
                $deliveryOrder = DeliveryOrder::findOne(['logistic_no' => $orderNo]);
                $logisticId = $deliveryOrder->logistic_id;
                if (in_array($logisticId, [3, 48089])) {
                    $deliveryInfoRes = KdApi::getDeliveryInfo($orderNo, 'zhongtong'); //获取物流轨迹
                    if (!$deliveryInfoRes['success']) {
                        throw new Exception("快递单号：" . $orderNo . "获取物流轨迹失败，原因：" . $deliveryInfoRes['msg']);
                    }
                    if (empty($deliveryInfoRes['data'])) {
                        throw new Exception("快递单号：" . $orderNo . "获取物流轨迹失败，原因：轨迹信息为空！");
                    }
                    foreach ($deliveryInfoRes['data'] as $key => $datum) {
                        $deliverySteps[$key]['operationDescribe'] = $datum['context'];
                        $deliverySteps[$key]['operationTime'] = $datum['time'];
                    }
                } else {
                    $deliveryInfoRes = EmsCloud::getDeliveryInfo($orderNo); //获取物流轨迹
                    if (empty($deliveryInfoRes['traces'])) {
                        throw new Exception("快递单号：" . $orderNo . "获取物流轨迹失败，原因：轨迹信息为空！");
                    }
                    foreach ($deliveryInfoRes['traces'] as $key => $value) {
                        $deliverySteps[$key]['operationDescribe'] = $value['remark'];
                        $deliverySteps[$key]['operationTime'] = $value['acceptTime'];
                    }
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '快递单号：' . $orderNo . '调用宅急送接口失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['zjs-delivery-info']);
        }
        return $this->render('zjs-delivery-info', [
            'deliverySteps' => $deliverySteps
        ]);
    }

    public function actionInWarehouse()
    {
        $deliveryOrderModel = [];
        $barcodeSvg = '';
        $warehouse = [
            'name' => '',
            'contact_name' => '',
            'contact_phone' => '',
            'contact_address' => ''
        ];
        try {
            if ($this->request->isPost) {
                $logisticNo = $this->request->post()['logistic_no'];
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                $warehouse = Warehouse::findOne(['code' => $deliveryOrderModel->warehouse_code]);
                $barcodeGenerator = new BarcodeGeneratorSVG();
                $barcodeSvg = $barcodeGenerator->getBarcode($deliveryOrderModel->shipping_no, $barcodeGenerator::TYPE_CODE_128, 1.00);
                if (!$deliveryOrderModel) {
                    throw new Exception("快递单号：" . $logisticNo . "查询失败，原因：不存在的订单！");
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['in-warehouse']);
        }
        return $this->render('in-warehouse', [
            'model' => $deliveryOrderModel,
            'barcodeSvg' => $barcodeSvg,
            'warehouseModel' => $warehouse
        ]);
    }


    /**
     * Lists all DeliveryOrder models.
     *
     * @return string
     */
    public function actionBatchUpdateStatus()
    {
        $params = $this->request->queryParams;
        $searchModel = new DeliveryOrderSearch();
        if (!empty($params)) {
            $isPost = true;
        } else {
            $isPost = false;
        }
        $dataProvider = $searchModel->search($params, $isPost);
        if ($isPost) {
            $totalCount = isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0;
        } else {
            $totalCount = 0;
        }
        $pages = new Pagination(
            [
                'totalCount' => $totalCount,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('batch-update-status', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    public function actionAjaxBatchUpdateStatus()
    {
        $return = [
            'msg' => '',
        ];
        try {
            if (\Yii::$app->request->isPost) {
                $post = \Yii::$app->request->post();
                $ids = $post['ids'];
                $status = $post['status'];
                $ids = substr($ids, 0, strlen($ids) - 1);
                $ids = explode(',', $ids);
                DeliveryOrder::updateAll(['status' => $status], ['in', 'id', $ids]);
            }
            $return['msg'] = '更新成功！';
        } catch (\Exception $e) {
            $return['msg'] = '批量更新订单状态失败，原因：' . Utility::arrayToString($e->getMessage());
        }

        exit(Json::encode($return));
    }


    public function actionAjaxInWarehouse()
    {
        $return = [
            'msg' => '',
        ];
        try {
            if (\Yii::$app->request->isPost) {
                $post = \Yii::$app->request->post();
                $logisticNo = $post['logistic_no'];
                DeliveryOrder::updateAll(['status' => DeliveryOrder::STATUS_REJECT_IN_WAREHOUSE], ['logistic_no' => $logisticNo]);
            }
            $return['msg'] = '更新成功！';
        } catch (\Exception $e) {
            $return['msg'] = '更新状态收已入库失败，原因：' . Utility::arrayToString($e->getMessage());
        }

        exit(Json::encode($return));
    }

}

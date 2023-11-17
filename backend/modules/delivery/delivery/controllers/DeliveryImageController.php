<?php

namespace backend\modules\delivery\controllers;

use common\components\TencentCloud;
use common\components\Utility;
use common\models\DeliveryImage;
use common\models\DeliveryImageSearch;
use common\models\DeliveryOrder;
use common\models\LogisticImage;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeliveryImageController implements the CRUD actions for DeliveryImage model.
 */
class DeliveryImageController extends Controller
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
     * Lists all DeliveryImage models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DeliveryImageSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DeliveryImage model.
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
     * Creates a new DeliveryImage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DeliveryImage();

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
     * Updates an existing DeliveryImage model.
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
     * Deletes an existing DeliveryImage model.
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
     * Finds the DeliveryImage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DeliveryImage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeliveryImage::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateReceiverInfoByImage()
    {
        $deliveryImageData['logistic_no'] = '';
        $deliveryImageData['name'] = '';
        $deliveryImageData['phone'] = '';
        $deliveryImageData['file_path'] = '';
        $deliveryImageData['image_base64'] = '';
        $deliveryImageData['text'] = '';
        $filePath = '';
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($this->request->isPost) {
                $logisticNo = $this->request->post()['logistic_no'];

                $model = DeliveryImage::findOne(['logistic_no' => $logisticNo]);

                $logisticImage = LogisticImage::findOne(['logistic_no' => $logisticNo]);
                if (!$logisticImage) {
                    throw new \Exception('没有上传图片！');
                }
                if (!$model) {
                    $model = new DeliveryImage();
                    $model->create_time = date('Y-m-d H:i:s', time());
                } else {
                    if (empty($model->image_path)) {
                        $path = __DIR__ . '/../../../../' . 'backend/web/uploads/logistic_image/' . date('Y-m-d', time()) . '/';
                        $filePath = $path . $logisticNo . '.jpg';
                        //创建目录失败
                        if (!file_exists($path) && !mkdir($path, 0777, true)) {
                            throw new \Exception('创建文件夹失敗！');
                        } else if (!is_writeable($path)) {
                            throw new \Exception('文件夹不可写！');
                        }
                        file_put_contents($filePath, base64_decode($filePath));
                        $model->image_path = $filePath;
                    }
                }

                $orcRes = TencentCloud::analysisOcrData($logisticImage->image_base64_str);
                if (!$orcRes['success']) {
                    throw new \Exception("快递单号：" . $logisticNo . " 图片识别失败，原因：" . $orcRes['msg']);
                }

                $name = $orcRes['data']['name'];
                $phone = $orcRes['data']['phone'];
                $text = $orcRes['data']['text'];
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                if (!$deliveryOrderModel) {
                    throw new \Exception("快递单号：" . $logisticNo . "不存在");
                }
                $deliveryOrderModel->device_receiver_name = $name;
                $deliveryOrderModel->device_receiver_phone = $phone;
                $deliveryOrderModel->update_name = 'system';
                $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                if (!$deliveryOrderModel->save()) {
                    throw new \Exception("快递单号：" . $logisticNo . "  更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                }

                //保存图片解析数据
                $model->logistic_no = $logisticNo;
                $model->image_data = $text;
                if (!$model->save()) {
                    throw new \Exception("快递单号：" . $logisticNo . "  新增运单图片解析数据失败，原因：" . Utility::arrayToString($model->getErrors()));
                }
                $transaction->commit();
                $deliveryImageData['logistic_no'] = $logisticNo;
                $deliveryImageData['name'] = $name;
                $deliveryImageData['phone'] = $phone;
                $deliveryImageData['file_path'] = $filePath;
                $deliveryImageData['image_base64'] = $logisticImage->image_base64_str;
                $deliveryImageData['text'] = $text;
                \Yii::$app->session->setFlash('success', '运单号：' . $logisticNo . '手动解析成功，已更新运单信息!');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', '运单号：' . $logisticNo . '手动解析失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['update-receiver-info-by-image']);
        }
        return $this->render('update-receiver-info-by-image', [
            'deliveryImageData' => $deliveryImageData
        ]);
    }


}

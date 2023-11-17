<?php

namespace backend\modules\finance\controllers;

use backend\models\CustomerRecharge;
use backend\models\CustomerRechargeSearch;
use backend\models\Institution;
use common\components\Utility;
use common\models\CustomerBalance;
use common\models\CustomerBalanceLog;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerRechargeController implements the CRUD actions for CustomerRecharge model.
 */
class CustomerRechargeController extends Controller
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
     * Lists all CustomerRecharge models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $params = $this->request->queryParams;
        $level = $institutionRes->level;
        if ($level != Institution::LEVEL_PARENT) {
            $params['CustomerRechargeSearch']['institution_id'] = $institutionId;
        }
        $searchModel = new CustomerRechargeSearch();
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
            'level' => $level
        ]);
    }

    /**
     * Displays a single CustomerRecharge model.
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
     * Creates a new CustomerRecharge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $model = new CustomerRecharge();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                if (!$model->load($post)) {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
                $rechargeOrderNo = CustomerRecharge::generateId();
                $file = $_FILES;
                if (empty($file['CustomerRecharge']['name']['pay_image_path']) || empty($file['CustomerRecharge']['tmp_name']['pay_image_path'])) {
                    throw new \Exception('支付凭证图片不能为空');
                }
                if ($file['CustomerRecharge']['type']['pay_image_path'] != 'image/jpeg') {
                    throw new \Exception('支付凭证图片格式只支持jpg或jpeg');
                }
                if ($file['CustomerRecharge']['size']['pay_image_path'] > 2097152) {
                    throw new \Exception('支付凭证图片不能超过2MB');
                }
                $path = 'uploads/pay_image/' . date('Y-m-d', time()) . '/';
                $filePath = $path . $rechargeOrderNo . '.jpg';
                //创建目录失败
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    throw new \Exception('创建文件夹失敗！');
                } else if (!is_writeable($path)) {
                    throw new \Exception('文件夹不可写！');
                }
                if (!move_uploaded_file($file['CustomerRecharge']['tmp_name']['pay_image_path'], $filePath)) {
                    throw new \Exception('复制文件失敗');
                }
                $invoiceFilePath = '';
                if (!empty($file['CustomerRecharge']['name']['invoice_image_path']) && !empty($file['CustomerRecharge']['tmp_name']['invoice_image_path'])) {
                    if ($file['CustomerRecharge']['type']['invoice_image_path'] != 'image/jpeg') {
                        throw new \Exception('发票凭证图片格式只支持jpg或jpeg');
                    }
                    if ($file['CustomerRecharge']['size']['invoice_image_path'] > 2097152) {
                        throw new \Exception('发票凭证图片不能超过2MB');
                    }
                    $invoicePath = 'uploads/invoice_image/' . date('Y-m-d', time()) . '/';
                    $invoiceFilePath = $invoicePath . $rechargeOrderNo . '.jpg';
                    //创建目录失败
                    if (!file_exists($invoicePath) && !mkdir($invoicePath, 0777, true)) {
                        throw new \Exception('创建文件夹失敗！');
                    } else if (!is_writeable($invoicePath)) {
                        throw new \Exception('文件夹不可写！');
                    }
                    if (!move_uploaded_file($file['CustomerRecharge']['tmp_name']['invoice_image_path'], $invoiceFilePath)) {
                        throw new \Exception('复制文件失敗');
                    }
                }
                $model->institution_id = $institutionId;
                $model->recharge_order_no = $rechargeOrderNo;
                $model->create_name = \Yii::$app->user->getIdentity()['username'];
                $model->create_time =  date('Y-m-d H:i:s', time());
                $model->pay_confirm_name = \Yii::$app->user->getIdentity()['username'];
                $model->pay_confirm_time = date('Y-m-d H:i:s', time());
                $model->pay_image_path = '/' . $filePath;
                $model->pay_image_path = '/' . $invoiceFilePath;
                if ($model->save()) {
                    $customerBalance = CustomerBalance::findOne(['institution_id' => $model->attributes['institution_id'], 'customer_id' => $model->attributes['customer_id']]);
                    if (!$customerBalance) {
                        throw new \Exception('用户余额不存在');
                    }
                    $beforeBalance = $customerBalance->balance;
                    //余额
                    $customerBalance->balance = $customerBalance->balance + $model->attributes['amount'];
                    $customerBalance->last_recharge_time = $model->attributes['create_name'];
                    $customerBalance->last_operation_detail = '充值';
                    $customerBalance->last_recharge_notes = $model->attributes['notes'];
                    $customerBalance->update_username = \Yii::$app->user->getIdentity()['username'];
                    $customerBalance->update_time = date('Y-m-d H:i:s', time());
                    if ($customerBalance->save()) {
                        //余额变更记录
                        $customerBalanceLog = new CustomerBalanceLog();
                        $customerBalanceLog->source = $model->attributes['recharge_order_no'];
                        $customerBalanceLog->institution_id = $model->attributes['institution_id'];
                        $customerBalanceLog->customer_id = $model->attributes['customer_id'];
                        $customerBalanceLog->change_amount = $model->attributes['amount'];
                        $customerBalanceLog->before_balance = $beforeBalance;
                        $customerBalanceLog->after_balance = $customerBalance->attributes['balance'];
                        $customerBalanceLog->type = CustomerBalanceLog::TYPE_ADD;
                        $customerBalanceLog->category = CustomerBalanceLog::CATEGORY_RECHARGE;
                        $customerBalanceLog->change_time = date('Y-m-d H:i:s', time());
                        if (!$customerBalanceLog->save()) {
                            throw new \Exception('新增余额变更记录失败，原因：' . Utility::arrayToString($customerBalanceLog->getErrors()));
                        }

                    } else {
                        throw new \Exception('更新余额失败，原因：' . Utility::arrayToString($customerBalance->getErrors()));
                    }
                    $transaction->commit();
                    \Yii::$app->session->setFlash('success', '新增客户充值成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'level' => $level,
                    'institutionId' => $institutionId

                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', '新增客户充值失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing CustomerRecharge model.
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
     * Deletes an existing CustomerRecharge model.
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
     * Finds the CustomerRecharge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CustomerRecharge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerRecharge::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

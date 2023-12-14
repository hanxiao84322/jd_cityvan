<?php

namespace backend\modules\finance\controllers;

use common\components\Utility;
use common\models\DeliveryOrder;
use common\models\LogisticCompanyCheckBill;
use common\models\LogisticCompanyCheckBillDetail;
use common\models\LogisticCompanySettlementOrder;
use common\models\LogisticCompanySettlementOrderDiscountsReductions;
use common\models\LogisticCompanySettlementOrderSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogisticCompanySettlementOrderController implements the CRUD actions for LogisticCompanySettlementOrder model.
 */
class LogisticCompanySettlementOrderController extends Controller
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
     * Lists all LogisticCompanySettlementOrder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticCompanySettlementOrderSearch();
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
     * Displays a single LogisticCompanySettlementOrder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $logisticCompanyCheckBillModel = LogisticCompanyCheckBill::findOne(['logistic_company_check_bill_no' => $model->logistic_company_check_bill_no]);
        return $this->render('view', [
            'model' => $model,
            'logisticCompanyCheckBillModel' => $logisticCompanyCheckBillModel,
        ]);
    }

    /**
     * Creates a new LogisticCompanySettlementOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        try {
            $id = $this->request->queryParams['id'];
            $logisticCompanyCheckBillModel = LogisticCompanyCheckBill::findOne($id);
            $logisticCompanySettlementOrderExists = LogisticCompanySettlementOrder::findOne(['logistic_company_check_bill_no' => $logisticCompanyCheckBillModel->logistic_company_check_bill_no]);
            if ($logisticCompanySettlementOrderExists) {
                throw new \Exception('该对账单已经创建过结算单了，结算单号:' . $logisticCompanySettlementOrderExists->settlement_order_no);
            }
            $model = new LogisticCompanySettlementOrder();
            $model->settlement_order_no = LogisticCompanySettlementOrder::generateId();
            $model->logistic_company_check_bill_no = $logisticCompanyCheckBillModel->logistic_company_check_bill_no;
            $model->logistic_id = $logisticCompanyCheckBillModel->logistic_id;
            $model->warehouse_code = $logisticCompanyCheckBillModel->warehouse_code;
            $model->order_num = $logisticCompanyCheckBillModel->system_order_num;
            $model->need_pay_amount = $logisticCompanyCheckBillModel->system_order_price;
            $model->need_receipt_amount = 0;
            $model->expect_amount = $logisticCompanyCheckBillModel->system_order_price;
            $model->date = date('Y-m-d', time());
            if ($this->request->isPost) {
                $post = $this->request->post();
                if ($post['LogisticCompanySettlementOrder']['diff_adjust_plan'] == LogisticCompanySettlementOrder::DIFF_ADJUST_PLAN_INPUT) {
                    if (empty($post['LogisticCompanySettlementOrder']['input_amount'])) {
                        throw new \Exception('差异调整方案为"手动输入结算金额"则输入结算金额(元)不能为空');
                    }
                }
                $model->load($post);
                $model->type = LogisticCompanySettlementOrder::TYPE_PAY;
                if ($model->diff_adjust_plan == LogisticCompanySettlementOrder::DIFF_ADJUST_PLAN_INPUT) {
                    $model->need_pay_amount = $model->need_pay_amount;
                } else {
                    $model->need_pay_amount = $logisticCompanyCheckBillModel->system_order_price;
                }
                $adjustTermArr = [];
                $totalAdjustAmount = 0.00;
                if (!empty($post['adjust_amount'])) {
                    foreach ($post['adjust_amount'] as $key => $item) {
                        $adjustAmount = !empty($item) ? $item : 0.00;
                        $totalAdjustAmount += $adjustAmount;
                        $adjustTermArr[$key]['adjust_amount'] = $adjustAmount;
                        $adjustTermArr[$key]['adjust_content'] = !empty($post['adjust_content'][$key]) ? $post['adjust_content'][$key] : '';
                    }
                }

                $noPreferentialAmount = $model->need_pay_amount + $totalAdjustAmount;
                if ($model->discounts_reductions) {
                    $needAmount = LogisticCompanySettlementOrderDiscountsReductions::getAmount($model->discounts_reductions, $noPreferentialAmount);
                } else {
                    $needAmount = $noPreferentialAmount;
                }
                $model->preferential_amount = $noPreferentialAmount - $needAmount;

                $model->adjust_amount = $totalAdjustAmount;
                $model->need_amount = $needAmount;

                $adjustTermStr = json_encode($adjustTermArr);
                $model->adjust_term = $adjustTermStr;
                $model->create_name = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if (!$model->save()) {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                } else {
                    LogisticCompanyCheckBill::updateAll(['status' => LogisticCompanyCheckBill::STATUS_CREATE_SETTLEMENT], ['logistic_company_check_bill_no' => $logisticCompanyCheckBillModel->logistic_company_check_bill_no]);
                    \Yii::$app->session->setFlash('success', '结算单创建成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                $model->loadDefaultValues();
            }
            return $this->render('create', [
                'model' => $model,
                'logisticCompanyCheckBillModel' => $logisticCompanyCheckBillModel,
            ]);
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新建结算单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['/finance/logistic-company-check-bill/index']);
        }


    }

    /**
     * Updates an existing LogisticCompanySettlementOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        try {
            $logisticCompanyCheckBillModel = LogisticCompanyCheckBill::findOne($id);

            $model = $this->findModel($id);
            $model->expect_amount = $model->need_amount;

            $adjustTermList = LogisticCompanySettlementOrder::getUpdateAdjustTerm($model->adjust_term);
            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);
                $model->type = LogisticCompanySettlementOrder::TYPE_PAY;
                $model->need_pay_amount = $post['LogisticCompanySettlementOrder']['expect_amount'];
                $model->need_amount = $post['LogisticCompanySettlementOrder']['expect_amount'];
                $adjustTermArr = [];
                $totalAdjustAmount = 0.00;
                if (!empty($post['adjust_amount'])) {
                    foreach ($post['adjust_amount'] as $key => $item) {
                        $adjustAmount = !empty($item) ? $item : 0.00;
                        $totalAdjustAmount += $adjustAmount;
                        $adjustTermArr[$key]['adjust_amount'] = $adjustAmount;
                        $adjustTermArr[$key]['adjust_content'] = !empty($post['adjust_content'][$key]) ? $post['adjust_content'][$key] : '';
                    }
                }
                $adjustTermStr = json_encode($adjustTermArr);
                $model->adjust_term = $adjustTermStr;
                $model->adjust_amount = $totalAdjustAmount;
                $model->create_name = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if (!$model->save()) {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                } else {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                $model->loadDefaultValues();
            }
            return $this->render('update', [
                'model' => $model,
                'logisticCompanyCheckBillModel' => $logisticCompanyCheckBillModel,
                'adjustTermList' => $adjustTermList
            ]);
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '修改结算单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['/finance/logistic-company-check-bill/view', 'id' => $id]);
        }
    }

    /**
     * Deletes an existing LogisticCompanySettlementOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->session->setFlash('success', '结算单删除成功!');
        return $this->redirect(['index']);
    }

    /**
     * Finds the LogisticCompanySettlementOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticCompanySettlementOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticCompanySettlementOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionConfirm($id)
    {
        $model= $this->findModel($id);
        $model->status = LogisticCompanySettlementOrder::STATUS_CONFIRM;
        $model->save();
        \Yii::$app->session->setFlash('success', '结算单确认成功!');
        return $this->redirect(['index']);
    }

    public function actionFinish($id)
    {
        $model= $this->findModel($id);
        $model->status = LogisticCompanySettlementOrder::STATUS_PAID;
        $model->save();
        $deliveryOrderList = LogisticCompanyCheckBillDetail::find()->select("logistic_no")->where(['logistic_company_check_bill_no' => $model->logistic_company_check_bill_no])->column();
        DeliveryOrder::updateAll(['is_logistic_company_settle' => DeliveryOrder::YES], ['logistic_no' => $deliveryOrderList]);
        \Yii::$app->session->setFlash('success', '结算单已完成!');
        return $this->redirect(['index']);
    }


    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $logisticCompanyCheckBillModel = LogisticCompanyCheckBill::findOne(['logistic_company_check_bill_no' => $model->logistic_company_check_bill_no]);
        return $this->render('print', [
            'model' => $model,
            'logisticCompanyCheckBillModel' => $logisticCompanyCheckBillModel,
        ]);
    }
}

<?php

namespace backend\modules\finance\controllers;

use backend\models\Institution;
use common\components\Utility;
use common\models\AdjustOrder;
use common\models\AdjustOrderSearch;
use common\models\CustomerSettlementOrder;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdjustOrderController implements the CRUD actions for AdjustOrder model.
 */
class AdjustOrderController extends Controller
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
     * Lists all AdjustOrder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $params = $this->request->queryParams;
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $params['AdjustOrderSearch']['institution_id'] = $institutionId;
        $searchModel = new AdjustOrderSearch();
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
            'institutionId' => $institutionId,
            'level' => $level,
        ]);
    }

    /**
     * Displays a single AdjustOrder model.
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
     * Creates a new AdjustOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $transaction = \Yii::$app->db->beginTransaction();
        $model = new AdjustOrder();
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                $settlementModel = CustomerSettlementOrder::findOne(['settlement_order_no' => $post['AdjustOrder']['settlement_no']]);
                $model->load($post);
                $model->adjust_order_no = AdjustOrder::generateId();
                $model->institution_id = $institutionId;
                $model->customer_id = $settlementModel->customer_id;
                $model->create_name = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    if ($model->type == AdjustOrder::TYPE_REWARD) { //奖励减应结算金额
                        $settlementModel->need_amount = $settlementModel->need_amount - $model->adjust_amount;
                        $settlementModel->adjust_amount = $settlementModel->adjust_amount - $model->adjust_amount;
                    } else { //罚款加应结算金额
                        $settlementModel->need_amount = $settlementModel->need_amount + $model->adjust_amount;
                        $settlementModel->adjust_amount = $settlementModel->adjust_amount + $model->adjust_amount;
                    }
                    if ($settlementModel->save()) {
                        $transaction->commit();
                        \Yii::$app->session->setFlash('success', '新建调整单成功!');
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        throw new \Exception(Utility::arrayToString($settlementModel->getErrors()));
                    }
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'institutionId' => $institutionId,
                    'level' => $level,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', '新建调整单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['create']);
        }

    }

    /**
     * Updates an existing AdjustOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $transaction = \Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                $settlementModel = CustomerSettlementOrder::findOne(['settlement_order_no' => $post['AdjustOrder']['settlement_no']]);
                $model->load($post);
                $model->adjust_order_no = AdjustOrder::generateId();
                $model->institution_id = $institutionId;
                $model->customer_id = $settlementModel->customer_id;
                $model->create_name = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    if ($model->type == AdjustOrder::TYPE_REWARD) { //奖励减应结算金额
                        $settlementModel->need_amount = $settlementModel->need_amount - $model->adjust_amount;
                        $settlementModel->adjust_amount = $settlementModel->adjust_amount - $model->adjust_amount;
                    } else { //罚款加应结算金额
                        $settlementModel->need_amount = $settlementModel->need_amount + $model->adjust_amount;
                        $settlementModel->adjust_amount = $settlementModel->adjust_amount + $model->adjust_amount;
                    }
                    if ($settlementModel->save()) {
                        $transaction->commit();
                        \Yii::$app->session->setFlash('success', '新建调整单成功!');
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        throw new \Exception(Utility::arrayToString($settlementModel->getErrors()));
                    }
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'institutionId' => $institutionId,
                    'level' => $level,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', '新建调整单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['create']);
        }
    }

    /**
     * Deletes an existing AdjustOrder model.
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
     * Finds the AdjustOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return AdjustOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdjustOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

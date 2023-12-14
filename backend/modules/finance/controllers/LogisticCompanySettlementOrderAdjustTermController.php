<?php

namespace backend\modules\finance\controllers;

use common\components\Utility;
use common\models\LogisticCompanySettlementOrderAdjustTerm;
use common\models\LogisticCompanySettlementOrderAdjustTermSearch;
use Psy\Util\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogisticCompanySettlementOrderAdjustTermController implements the CRUD actions for LogisticCompanySettlementOrderAdjustTerm model.
 */
class LogisticCompanySettlementOrderAdjustTermController extends Controller
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
     * Lists all LogisticCompanySettlementOrderAdjustTerm models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticCompanySettlementOrderAdjustTermSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LogisticCompanySettlementOrderAdjustTerm model.
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
     * Creates a new LogisticCompanySettlementOrderAdjustTerm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LogisticCompanySettlementOrderAdjustTerm();

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
     * Updates an existing LogisticCompanySettlementOrderAdjustTerm model.
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
     * Deletes an existing LogisticCompanySettlementOrderAdjustTerm model.
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
     * Finds the LogisticCompanySettlementOrderAdjustTerm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticCompanySettlementOrderAdjustTerm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticCompanySettlementOrderAdjustTerm::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAjaxCreate()
    {
        $return = [
            'status' => 0,
            'errMsg' => ''
        ];
        $adjustAmount = $this->request->post()['adjust_amount'];
        $adjustContent = $this->request->post()['adjust_content'];
        $settlementOrderNo = $this->request->post()['settlement_order_no'];

        $logisticCompanySettlementOrderAdjustTerm = new LogisticCompanySettlementOrderAdjustTerm();
        $logisticCompanySettlementOrderAdjustTerm->settlement_order_no = $settlementOrderNo;
        $logisticCompanySettlementOrderAdjustTerm->amount = $adjustAmount;
        $logisticCompanySettlementOrderAdjustTerm->content = $adjustContent;
        if (!$logisticCompanySettlementOrderAdjustTerm->save()) {
            $return['errMsg'] = Utility::arrayToString($logisticCompanySettlementOrderAdjustTerm->getErrors());
        } else {
            $return['status'] = 1;
        }
        echo Json::encode($return);
        exit;
    }

}

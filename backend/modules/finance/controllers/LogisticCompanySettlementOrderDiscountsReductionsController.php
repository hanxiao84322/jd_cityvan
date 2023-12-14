<?php

namespace backend\modules\finance\controllers;

use common\models\LogisticCompanySettlementOrderDiscountsReductions;
use common\models\LogisticCompanySettlementOrderDiscountsReductionsSearch;
use Psy\Util\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogisticCompanySettlementOrderDiscountsReductionsController implements the CRUD actions for LogisticCompanySettlementOrderDiscountsReductions model.
 */
class LogisticCompanySettlementOrderDiscountsReductionsController extends Controller
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
     * Lists all LogisticCompanySettlementOrderDiscountsReductions models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticCompanySettlementOrderDiscountsReductionsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LogisticCompanySettlementOrderDiscountsReductions model.
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
     * Creates a new LogisticCompanySettlementOrderDiscountsReductions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LogisticCompanySettlementOrderDiscountsReductions();

        if ($this->request->isPost) {
            $post = $this->request->post();
            $model->load($post);
            $model->create_username = \Yii::$app->user->getIdentity()['username'];
            $model->create_time =  date('Y-m-d H:i:s', time());
            if ($model->save()) {
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
     * Updates an existing LogisticCompanySettlementOrderDiscountsReductions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $post = $this->request->post();
            $model->load($post);
            $model->update_username = \Yii::$app->user->getIdentity()['username'];
            $model->update_time =  date('Y-m-d H:i:s', time());
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LogisticCompanySettlementOrderDiscountsReductions model.
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
     * Finds the LogisticCompanySettlementOrderDiscountsReductions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticCompanySettlementOrderDiscountsReductions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticCompanySettlementOrderDiscountsReductions::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionAjaxGetAmount()
    {
        $return = [
            'status' => 0,
            'errMsg' => '',
            'discounts_reductions_amount' => 0.00
        ];
        $id = $this->request->post()['discounts_reductions_id'];
        $expectAmount = $this->request->post()['expect_amount'];
        $discountsReductionsAmount = LogisticCompanySettlementOrderDiscountsReductions::getAmount($id, $expectAmount);
        $return['status'] = 1;
        $return['discounts_reductions_amount'] = $discountsReductionsAmount;
        echo Json::encode($return);
        exit;
    }
}

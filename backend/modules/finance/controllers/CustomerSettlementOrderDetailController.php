<?php

namespace backend\modules\finance\controllers;

use common\models\CustomerSettlementOrderDetail;
use common\models\CustomerSettlementOrderDetailSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerSettlementOrderDetailController implements the CRUD actions for CustomerSettlementOrderDetail model.
 */
class CustomerSettlementOrderDetailController extends Controller
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
     * Lists all CustomerSettlementOrderDetail models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $params = $this->request->queryParams;
        $searchModel = new CustomerSettlementOrderDetailSearch();
        $dataProvider = $searchModel->search($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('index', [
            'settlement_order_no' => $searchModel->settlement_order_no,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }


    /**
     * Lists all CustomerSettlementOrderDetail models.
     *
     * @return string
     */
    public function actionExport()
    {
        $params = $this->request->queryParams;
        $searchModel = new CustomerSettlementOrderDetailSearch();
        $searchModel->exportData($params);
    }

    /**
     * Displays a single CustomerSettlementOrderDetail model.
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
     * Creates a new CustomerSettlementOrderDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CustomerSettlementOrderDetail();

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
     * Updates an existing CustomerSettlementOrderDetail model.
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
     * Deletes an existing CustomerSettlementOrderDetail model.
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
     * Finds the CustomerSettlementOrderDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CustomerSettlementOrderDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerSettlementOrderDetail::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

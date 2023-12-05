<?php

namespace backend\modules\report\controllers;

use backend\models\UserBackend;
use common\models\DeliveryOrderOverdueWarning;
use common\models\DeliveryOrderOverdueWarningSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeliveryOrderOverdueWarningController implements the CRUD actions for DeliveryOrderOverdueWarning model.
 */
class DeliveryOrderOverdueWarningController extends Controller
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
     * Lists all DeliveryOrderOverdueWarning models.
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

        $searchModel = new DeliveryOrderOverdueWarningSearch();
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
            'create_time_start' => $searchModel->create_time_start,
            'create_time_end' => $searchModel->create_time_end
        ]);
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionItems()
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
        $type = $params['type'];
        $createTimeStart = $params['create_time_start'];
        $createTimeEnd = $params['create_time_end'];
        $warehouseCode = $params['warehouse_code'];
        $logisticId = $params['logistic_id'];
        $searchModel = new DeliveryOrderOverdueWarningSearch();
        $dataProvider = $searchModel->searchItems($type, $createTimeStart, $createTimeEnd, $warehouseCode, $logisticId);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        switch ($type) {
            case '1': //无揽收
                $typeName = '超期1天内';
                break;
            case '2': //无运输
                $typeName = '超期1-2天';
                break;
            case '3': //超时揽收
                $typeName = '超期2-3天';
                break;
            case '4': //超时运输
                $typeName = '超期3-5天';
                break;
            case '5': //超时运输
                $typeName = '超期5-7天';
                break;
            case '6': //超时运输
                $typeName = '7天以上严重超期';
                break;
            default :
                $typeName = '';
                break;
        }
        return $this->render('items', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'typeName' => $typeName,
            'type' => $type,
            'create_time_start' => $createTimeStart,
            'create_time_end' => $createTimeEnd,
            'warehouse_code' => $warehouseCode,
            'logistic_id' => $logisticId
        ]);
    }

    public function actionItemsExport()
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
        $type = $params['type'];
        $createTimeStart = $params['create_time_start'];
        $createTimeEnd = $params['create_time_end'];
        $warehouseCode = $params['warehouse_code'];
        $logisticId = $params['logistic_id'];
        switch ($type) {
            case '1': //无揽收
                $typeName = '超期1天内';
                break;
            case '2': //无运输
                $typeName = '超期1-2天';
                break;
            case '3': //超时揽收
                $typeName = '超期2-3天';
                break;
            case '4': //超时运输
                $typeName = '超期3-5天';
                break;
            case '5': //超时运输
                $typeName = '超期5-7天';
                break;
            case '6': //超时运输
                $typeName = '7天以上严重超期';
                break;
            default :
                $typeName = '';
                break;
        }
        $searchModel = new DeliveryOrderOverdueWarningSearch();
        $searchModel->searchItemsExport($type, $createTimeStart, $createTimeEnd, $warehouseCode, $logisticId, $typeName);
    }

    /**
     * Displays a single DeliveryOrderOverdueWarning model.
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
     * Creates a new DeliveryOrderOverdueWarning model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DeliveryOrderOverdueWarning();

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
     * Updates an existing DeliveryOrderOverdueWarning model.
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
     * Deletes an existing DeliveryOrderOverdueWarning model.
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
     * Finds the DeliveryOrderOverdueWarning model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DeliveryOrderOverdueWarning the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeliveryOrderOverdueWarning::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

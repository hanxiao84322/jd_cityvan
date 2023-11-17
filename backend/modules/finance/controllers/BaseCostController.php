<?php

namespace backend\modules\finance\controllers;

use common\components\Utility;
use common\models\BaseCost;
use common\models\BaseCostSearch;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BaseCostController implements the CRUD actions for BaseCost model.
 */
class BaseCostController extends Controller
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
     * Lists all BaseCost models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BaseCostSearch();
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
     * Displays a single BaseCost model.
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
     * Creates a new BaseCost model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new BaseCost();

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
     * Updates an existing BaseCost model.
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
     * Deletes an existing BaseCost model.
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
     * Finds the BaseCost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BaseCost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BaseCost::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAjaxCopy($id)
    {
        $return = [
            'success' => 0,
            'msg' => ''
        ];
        $model = $this->findModel($id);
        $newModel = new BaseCost();
        $newModel->warehouse = $model->attributes['warehouse'];
        $newModel->month = date('Y-m', time());
        $newModel->data_service_fee = $model->attributes['data_service_fee'];
        $newModel->month_rent = $model->attributes['month_rent'];
        $newModel->worker_num = $model->attributes['worker_num'];
        $newModel->worker_fee = $model->attributes['worker_fee'];
        $newModel->device_fee = $model->attributes['device_fee'];
        $newModel->create_time = date('Y-m-d H:i:s', time());
        $newModel->create_name = \Yii::$app->user->getIdentity()['username'];

        if ($newModel->save()) {
            $return['status'] = 1;
            $return['msg'] = '复制新增成功';
        } else {
            $return['msg'] = '复制新增失败，原因：' . Utility::arrayToString($newModel->getErrors());
        }
        exit(Json::encode($return));
    }

}

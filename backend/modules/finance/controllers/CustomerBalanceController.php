<?php

namespace backend\modules\finance\controllers;

use backend\models\Institution;
use common\models\Customer;
use common\models\CustomerBalance;
use common\models\CustomerBalanceLogSearch;
use common\models\CustomerBalanceSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerBalanceController implements the CRUD actions for CustomerBalance model.
 */
class CustomerBalanceController extends Controller
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
     * Lists all CustomerBalance models.
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
            $params['CustomerBalanceSearch']['institution_id'] = $institutionId;
        }
        $searchModel = new CustomerBalanceSearch();
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
     * Displays a single CustomerBalance model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $model = $this->findModel($id);
        $searchModel = new CustomerBalanceLogSearch();
        $params = $this->request->queryParams;
        $params['CustomerBalanceLogSearch']['institution_id'] = $institutionId;
        $params['CustomerBalanceLogSearch']['customer_id'] = $model->customer_id;
        $dataProvider = $searchModel->search($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('view', [
            'model' =>  $model,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Creates a new CustomerBalance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CustomerBalance();

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
     * Updates an existing CustomerBalance model.
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
     * Deletes an existing CustomerBalance model.
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
     * Finds the CustomerBalance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CustomerBalance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerBalance::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionMy()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $parentInstitutionId = Institution::getParentIdById($institutionId);
        $institution = Institution::findOne($institutionId);
        $myCustomer = Customer::findOne(['name' => $institution->name]);
        $model = CustomerBalance::findOne(['customer_id' => $myCustomer->id]);
        $searchModel = new CustomerBalanceLogSearch();
        $params['CustomerBalanceLogSearch']['institution_id'] = $parentInstitutionId;
        $params['CustomerBalanceLogSearch']['customer_id'] = $myCustomer->id;
        $dataProvider = $searchModel->search($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('my', [
            'model' =>  $model,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }


}

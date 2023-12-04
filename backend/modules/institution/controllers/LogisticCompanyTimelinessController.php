<?php

namespace backend\modules\institution\controllers;

use common\models\Cnarea;
use common\models\LogisticCompanyTimeliness;
use common\models\LogisticCompanyTimelinessSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogisticCompanyTimelinessController implements the CRUD actions for LogisticCompanyTimeliness model.
 */
class LogisticCompanyTimelinessController extends Controller
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
     * Lists all LogisticCompanyTimeliness models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticCompanyTimelinessSearch();
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
            'pages' => $pages
        ]);
    }

    /**
     * Displays a single LogisticCompanyTimeliness model.
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
     * Creates a new LogisticCompanyTimeliness model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LogisticCompanyTimeliness();

        if ($this->request->isPost) {
            $post = $this->request->post();
            $post['LogisticCompanyTimeliness']['province'] = Cnarea::getNameByCode($post['LogisticCompanyTimeliness']['province_code']);
            $post['LogisticCompanyTimeliness']['city'] = empty($post['LogisticCompanyTimeliness']['city_code']) ? '' : Cnarea::getNameByCode($post['LogisticCompanyTimeliness']['city_code']);
            $post['LogisticCompanyTimeliness']['district'] = empty($post['LogisticCompanyTimeliness']['district_code']) ? '' : Cnarea::getNameByCode($post['LogisticCompanyTimeliness']['district_code']);
            if ($model->load($post) && $model->save()) {
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
     * Updates an existing LogisticCompanyTimeliness model.
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
            $post['LogisticCompanyTimeliness']['province'] = Cnarea::getNameByCode($post['LogisticCompanyTimeliness']['province_code']);
            $post['LogisticCompanyTimeliness']['city'] = Cnarea::getNameByCode($post['LogisticCompanyTimeliness']['city_code']);
            $post['LogisticCompanyTimeliness']['district'] = Cnarea::getNameByCode($post['LogisticCompanyTimeliness']['district_code']);
            if ($model->load($post) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LogisticCompanyTimeliness model.
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
     * Finds the LogisticCompanyTimeliness model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticCompanyTimeliness the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticCompanyTimeliness::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

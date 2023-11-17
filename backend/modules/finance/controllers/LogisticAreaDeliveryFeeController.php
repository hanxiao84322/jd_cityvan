<?php

namespace backend\modules\finance\controllers;

use common\components\Utility;
use common\models\Cnarea;
use common\models\LogisticAreaDeliveryFee;
use common\models\LogisticAreaDeliveryFeeSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogisticAreaDeliveryFeeController implements the CRUD actions for LogisticAreaDeliveryFee model.
 */
class LogisticAreaDeliveryFeeController extends Controller
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
     * Lists all LogisticAreaDeliveryFee models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticAreaDeliveryFeeSearch();
        $params = $this->request->queryParams;
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
            'cityList' => !empty($searchModel->province) ? Cnarea::getAllByParentCode($searchModel->province) : [],
            'districtList' => !empty($searchModel->city) ? Cnarea::getAllByParentCode($searchModel->city) : [],
        ]);
    }

    /**
     * Displays a single LogisticAreaDeliveryFee model.
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
     * Creates a new LogisticAreaDeliveryFee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $model = new LogisticAreaDeliveryFee();
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                if ($post['LogisticAreaDeliveryFee']['fee_type'] == LogisticAreaDeliveryFee::FEE_TYPE_RANGE) {
                    $post['LogisticAreaDeliveryFee']['fee_rules'] = json_encode($post['LogisticAreaDeliveryFee']['range_fee_data']);
                } else {
                    $post['LogisticAreaDeliveryFee']['fee_rules'] = json_encode($post['LogisticAreaDeliveryFee']['first_and_follow_fee_data']);
                }
                $post['LogisticAreaDeliveryFee']['institution_id'] =$institutionId;
                $post['LogisticAreaDeliveryFee']['create_user'] =\Yii::$app->user->getIdentity()['username'];
                $post['LogisticAreaDeliveryFee']['create_time'] = date('Y-m-d H:i:s', time());
                if ($model->load($post) && $model->save()) {
                    \Yii::$app->session->setFlash('success', '新增客户区域运费成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新增客户区域运费失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing LogisticAreaDeliveryFee model.
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
     * Deletes an existing LogisticAreaDeliveryFee model.
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
     * Finds the LogisticAreaDeliveryFee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticAreaDeliveryFee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticAreaDeliveryFee::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

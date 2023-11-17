<?php

namespace backend\modules\finance\controllers;

use backend\models\CustomerAreaDeliveryFee;
use backend\models\CustomerAreaDeliveryFeeSearch;
use common\components\Utility;
use common\models\Cnarea;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerAreaDeliveryFeeController implements the CRUD actions for CustomerAreaDeliveryFee model.
 */
class CustomerAreaDeliveryFeeController extends Controller
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
     * Lists all CustomerAreaDeliveryFee models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $searchModel = new CustomerAreaDeliveryFeeSearch();
        $params = $this->request->queryParams;
        $params['CustomerAreaDeliveryFeeSearch']['institution_id'] = $institutionId;
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
     * Displays a single CustomerAreaDeliveryFee model.
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
     * Creates a new CustomerAreaDeliveryFee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $model = new CustomerAreaDeliveryFee();
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                if ($post['CustomerAreaDeliveryFee']['fee_type'] == CustomerAreaDeliveryFee::FEE_TYPE_RANGE) {
                    $post['CustomerAreaDeliveryFee']['fee_rules'] = json_encode($post['CustomerAreaDeliveryFee']['range_fee_data']);
                } else {
                    $post['CustomerAreaDeliveryFee']['fee_rules'] = json_encode($post['CustomerAreaDeliveryFee']['first_and_follow_fee_data']);
                }
                $post['CustomerAreaDeliveryFee']['institution_id'] =$institutionId;
                $post['CustomerAreaDeliveryFee']['create_user'] =\Yii::$app->user->getIdentity()['username'];
                $post['CustomerAreaDeliveryFee']['create_time'] = date('Y-m-d H:i:s', time());
                if ($model->load($post) && $model->save()) {
                    \Yii::$app->session->setFlash('success', '新建客户区域运费成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'institutionId' => $institutionId,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新建客户区域运费失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing CustomerAreaDeliveryFee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $model = $this->findModel($id);
        try {
            if ($this->request->isPost) {
                $post = $this->request->post();
                if ($post['CustomerAreaDeliveryFee']['fee_type'] == CustomerAreaDeliveryFee::FEE_TYPE_RANGE) {
                    $post['CustomerAreaDeliveryFee']['fee_rules'] = json_encode($post['CustomerAreaDeliveryFee']['range_fee_data']);
                } else {
                    $post['CustomerAreaDeliveryFee']['fee_rules'] = json_encode($post['CustomerAreaDeliveryFee']['first_and_follow_fee_data']);
                }
                $post['CustomerAreaDeliveryFee']['update_user'] =\Yii::$app->user->getIdentity()['username'];
                $post['CustomerAreaDeliveryFee']['update_time'] = date('Y-m-d H:i:s', time());

                if ($model->load($post) && $model->save()) {
                    \Yii::$app->session->setFlash('success', '修改客户区域运费成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'institutionId' => $institutionId,
                ]);
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '修改客户区域运费失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }

    }

    /**
     * Deletes an existing CustomerAreaDeliveryFee model.
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
     * Finds the CustomerAreaDeliveryFee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CustomerAreaDeliveryFee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerAreaDeliveryFee::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

namespace backend\modules\customer\controllers;

use backend\models\CustomerAreaDeliveryFee;
use backend\models\Institution;
use common\components\Utility;
use common\models\Customer;
use common\models\CustomerBalance;
use common\models\CustomerSearch;
use Psy\Util\Json;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
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
     * Lists all Customer models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $params = $this->request->queryParams;
        if ($level != Institution::LEVEL_PARENT) {
            $params['CustomerSearch']['institution_id'] = $institutionId;
        }
        $searchModel = new CustomerSearch();
        $pageSize = \Yii::$app->request->get('page_size');
        if (!empty($pageSize)) {
            $searchModel->page_size = $pageSize;
        }
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
     * Displays a single Customer model.
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
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Customer();
        try {
            $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
            $institutionRes = Institution::findOne($institutionId);
            $level = $institutionRes->level;
            $customer = Customer::findOne(['name' => $institutionRes['name']]);
            if ($this->request->isPost) {
                $post = $this->request->post();
                $post['Customer']['parent_customer_id'] = $customer->id;
                $post['Customer']['type'] = Customer::TYPE_SELF;
                $post['Customer']['create_name'] = \Yii::$app->user->getIdentity()['username'];
                $post['Customer']['create_time'] = date('Y-m-d H:i:s', time());
                if ($model->load($post) && $model->save()) {
                    $customerBalance = new CustomerBalance();
                    $customerBalance->customer_id = $model->attributes['id'];
                    $customerBalance->institution_id = $model->attributes['institution_id'];
                    $customerBalance->balance = 0.00;
                    $customerBalance->update_time = date('Y-m-d H:i:s', time());
                    $customerBalance->update_username = \Yii::$app->user->getIdentity()['username'];
                    if (!$customerBalance->save()) {
                        throw new \Exception('创建余额信息失败,原因：' . Utility::arrayToString($model->getErrors()));
                    }
                    \Yii::$app->session->setFlash('success', '新增客户成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
            return $this->render('create', [
                'model' => $model,
                'institutionId' => $institutionId,
                'level' => $level
            ]);
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', '新增客户失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        if ($this->request->isPost) {
            $post = $this->request->post();
            $post['Customer']['update_name'] = \Yii::$app->user->getIdentity()['username'];
            $post['Customer']['update_time'] = date('Y-m-d H:i:s', time());
            if ($model->load($post) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'institutionId' => $institutionId,
            'level' => $level
        ]);
    }

    /**
     * Deletes an existing Customer model.
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
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAjaxGetListByTypeAndInstitutionId()
    {
        $post = $this->request->post();
        $institutionId = $post['institution_id'];
        $customerType = $post['customer_type'];
        $areaList = Customer::find()->where(['type' => $customerType, 'institution_id' => $institutionId])->asArray()->all();
        $return['status'] = 1;
        $html = '';
        foreach ($areaList as $area) {
            $html .= '<option value="' . $area['id'] . '">' . $area['name'] . '</option>';
        }
        $return['data'] = $html;

        exit(Json::encode($return));

    }
}

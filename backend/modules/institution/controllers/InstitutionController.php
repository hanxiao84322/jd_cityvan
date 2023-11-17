<?php

namespace backend\modules\institution\controllers;

use backend\models\Institution;
use backend\models\InstitutionSearch;
use common\components\Utility;
use common\models\CustomerBalance;
use yii\data\Pagination;
use yii\web\Controller;
use common\models\Customer;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InstitutionController implements the CRUD actions for Institution model.
 */
class InstitutionController extends Controller
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
     * Lists all Institution models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        $searchModel = new InstitutionSearch();
        $params = $this->request->queryParams;
        $params['InstitutionSearch']['parent_id'] = $institutionId;
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
            'isParent' => $level == Institution::LEVEL_PARENT ? 1 : 0
        ]);
    }

    /**
     * Displays a single Institution model.
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
     * Creates a new Institution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model = new Institution();
            $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
            $institutionRes = Institution::findOne($institutionId);
            $level = $institutionRes->level;

            if ($this->request->isPost) {
                $post = $this->request->post();
                $post['Institution']['belong_city_list'] = json_encode($post['Institution']['belong_city_list']);
                $post['Institution']['create_name'] = \Yii::$app->user->getIdentity()['username'];
                $post['Institution']['create_time'] = date('Y-m-d H:i:s', time());

                if ($model->load($post) && $model->save()) {
                    $institution = $model->attributes;
                    $customerCreateRes = Customer::createByInstitution($institution, \Yii::$app->user->getIdentity()['username']);
                    if ($customerCreateRes['success']) {
                        $customerBalance = new CustomerBalance();
                        $customerBalance->customer_id = $customerCreateRes['id'];
                        $customerBalance->institution_id = $institutionId;
                        $customerBalance->balance = 0.00;
                        $customerBalance->update_time = date('Y-m-d H:i:s', time());
                        $customerBalance->update_username = \Yii::$app->user->getIdentity()['username'];
                        if (!$customerBalance->save()) {
                            throw new \Exception('创建余额信息失败,原因：' . Utility::arrayToString($model->getErrors()));
                        }
                        $transaction->commit();
                        \Yii::$app->session->setFlash('success', '新增组织机构成功!');
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        throw new \Exception($customerCreateRes['msg']);
                    }
                }
            }
            return $this->render('create', [
                'model' => $model,
                'level' => $level,
                'institutionId' => $institutionId
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', '新增组织机构失败，原因：' . $e->getMessage() .  '!');
            return $this->redirect(['index']);
        }

    }

    /**
     * Updates an existing Institution model.
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
            $post['Institution']['belong_city_list'] = json_encode($post['Institution']['belong_city_list']);
            $post['Institution']['update_name'] = \Yii::$app->user->getIdentity()['username'];
            $post['Institution']['update_time'] = date('Y-m-d H:i:s', time());
            if ($model->load($post) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'level' => $level,
            'institutionId' => $institutionId
        ]);
    }

    /**
     * Deletes an existing Institution model.
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
     * Finds the Institution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Institution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Institution::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetParentsIdListByLevel($level)
    {
        $level = (int)$level - 1;
        $parentsCodeList = Institution::getParentsIdListByLevel($level);
        if (empty($parentsCodeList)) {
            echo "<option value='" . 1 . "'>" . "</option>";
        }
        foreach ($parentsCodeList as $value) {
            echo "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
        }
    }
}

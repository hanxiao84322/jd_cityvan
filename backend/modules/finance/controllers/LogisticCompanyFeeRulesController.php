<?php

namespace backend\modules\finance\controllers;

use common\components\Utility;
use common\models\Cnarea;
use common\models\LogisticCompanyFeeRules;
use common\models\LogisticCompanyFeeRulesSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogisticCompanyFeeRulesController implements the CRUD actions for LogisticCompanyFeeRules model.
 */
class LogisticCompanyFeeRulesController extends Controller
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
     * Lists all LogisticCompanyFeeRules models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticCompanyFeeRulesSearch();
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
     * Displays a single LogisticCompanyFeeRules model.
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
     * Creates a new LogisticCompanyFeeRules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        try {
            $model = new LogisticCompanyFeeRules();

            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);

                $continueWeightRule = [];
                $continueWeightRuleArr = explode("\r\n", $post['LogisticCompanyFeeRules']['continue_weight_rule']);
                if (empty($continueWeightRuleArr)) {
                    throw new \Exception('续重规则为空或者格式错误，请重新填写！');
                }
                foreach ($continueWeightRuleArr as $key => $item) {
                    if (!empty($item)) {
                        $continueWeightRule[$key] = explode(",", $item);
                    }
                }
                $model->province = Cnarea::getNameByCode($model->province_code);
                $model->city = Cnarea::getNameByCode($model->city_code);
                $model->district = Cnarea::getNameByCode($model->district_code);

                $model->continue_weight_rule = json_encode($continueWeightRule);
                $model->create_username = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '新建调整单成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                $model->loadDefaultValues();
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        } catch (\Exception $e) {
            \Yii::$app->session->set('formData', \Yii::$app->request->post());
            \Yii::$app->session->setFlash('error', '新建调整单失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['create']);
        }

    }

    /**
     * Updates an existing LogisticCompanyFeeRules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        try {
            $model = $this->findModel($id);
            $model->continue_weight_rule = LogisticCompanyFeeRules::getContinueWeightRoundRuleText($model->continue_weight_rule);
            if ($this->request->isPost) {
                $post = $this->request->post();
                $model->load($post);
                $continueWeightRule = [];
                $continueWeightRuleArr = explode("\r\n", $post['LogisticCompanyFeeRules']['continue_weight_rule']);
                if (empty($continueWeightRuleArr)) {
                    throw new \Exception('续重规则为空或者格式错误，请重新填写！');
                }
                foreach ($continueWeightRuleArr as $key => $item) {
                    if (!empty($item)) {
                        $continueWeightRule[$key] = explode(",", $item);
                    }
                }
                $model->province = Cnarea::getNameByCode($model->province_code);
                $model->city = Cnarea::getNameByCode($model->city_code);
                $model->district = Cnarea::getNameByCode($model->district_code);

                $model->continue_weight_rule = json_encode($continueWeightRule);
                $model->create_username = \Yii::$app->user->getIdentity()['username'];
                $model->create_time = date('Y-m-d H:i:s', time());
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '新建运费成功!');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    throw new \Exception(Utility::arrayToString($model->getErrors()));
                }
            } else {
                $model->loadDefaultValues();
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        } catch (\Exception $e) {
            \Yii::$app->session->set('formData', \Yii::$app->request->post());
            \Yii::$app->session->setFlash('error', '新建运费失败，原因：' . $e->getMessage() . '!');
            return $this->redirect(['update']);
        }
    }

    /**
     * Deletes an existing LogisticCompanyFeeRules model.
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
     * Finds the LogisticCompanyFeeRules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticCompanyFeeRules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticCompanyFeeRules::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

namespace backend\modules\userBackend\controllers;

use backend\models\Institution;
use backend\models\UserBackend;
use backend\models\UserBackendSearch;
use common\components\Utility;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserBackendController implements the CRUD actions for UserBackend model.
 */
class UserBackendController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            // 当前rule将会针对这里设置的actions起作用，如果actions不设置，默认就是当前控制器的所有操作
                            'actions' => ['index', 'view', 'create', 'update', 'delete', 'signup', 'update-password'],
                            // 设置actions的操作是允许访问还是拒绝访问
                            'allow' => true,
                            // @ 当前规则针对认证过的用户; ? 所有方可均可访问
                            'roles' => ['@'],
                        ],

                    ],
                ],
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
     * Lists all UserBackend models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $params = $this->request->queryParams;
        $searchModel = new UserBackendSearch();

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
        ]);
    }

    /**
     * Displays a single UserBackend model.
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
     * Creates a new UserBackend model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserBackend();

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
     * Updates an existing UserBackend model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost) {
            $post = \Yii::$app->request->post();
            $model->load($post);
            $model->warehouse_code_list = !empty($post['UserBackend']['warehouse_code_list']) ? json_encode($post['UserBackend']['warehouse_code_list']) : '';
            $model->logistic_id_list = !empty($post['UserBackend']['logistic_id_list']) ? json_encode($post['UserBackend']['logistic_id_list']) : '';
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                \Yii::$app->session->setFlash('error', '修改用户失败，原因：' . Utility::arrayToString($model->getErrors()) .  '!');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdatePassword()
    {
        $id = \Yii::$app->request->get('id');
        if (empty($id)) {
            $username = \Yii::$app->user->getIdentity()['username'];
            $userModel = UserBackend::findOne(['username' => $username]);
            $id = $userModel->id;
        }

        $model = $this->findModel($id);
        $password = Utility::generatePassword(16);
        if ($model->load(\Yii::$app->request->post()) && $model->updatePassword($id)) {
            return $this->redirect(['index']);
        }

        return $this->render('update-password', [
            'model' => $model,
            'password' => $password
        ]);
    }

    /**
     * Deletes an existing UserBackend model.
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
     * Finds the UserBackend model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return UserBackend the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserBackend::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     *  create new user
     */
    public function actionSignup()
    {
        // 实例化一个表单模型，这个表单模型我们还没有创建，等一下后面再创建
        $model = new \backend\models\SignupForm();
        // $model->signup() 方法, 是我们要实现的具体的添加用户操作
        if ($this->request->isPost) {
            $post = \Yii::$app->request->post();
            $model->load($post);
            $model->warehouse_code_list = !empty($post['SignupForm']['warehouse_code_list']) ? json_encode($post['SignupForm']['warehouse_code_list']) : '';
            $model->logistic_id_list = !empty($post['SignupForm']['logistic_id_list']) ? json_encode($post['SignupForm']['logistic_id_list']) : '';
            if ($model->signup()) {
                \Yii::$app->session->setFlash('success', '新建用户成功!');
                return $this->redirect(['index']);
            }
        }
        // 下面这一段是我们刚刚分析的第一个小问题的实现
        // 渲染添加新用户的表单
        return $this->render('signup', [
            'model' => $model
        ]);
    }
}

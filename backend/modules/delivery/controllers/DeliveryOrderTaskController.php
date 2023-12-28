<?php

namespace backend\modules\delivery\controllers;

use common\components\Utility;
use common\models\DeliveryOrderTask;
use common\models\DeliveryOrderTaskSearch;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeliveryOrderTaskController implements the CRUD actions for DeliveryOrderTask model.
 */
class DeliveryOrderTaskController extends Controller
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
     * Lists all DeliveryOrderTask models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DeliveryOrderTaskSearch();
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
     * Displays a single DeliveryOrderTask model.
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
     * Creates a new DeliveryOrderTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionAjaxCreate()
    {
        ini_set("max_execution_time", 300);
        ini_set('memory_limit', '128M');
        $return = [
            'status' => 0,
            'msg' => '',
        ];
        try {
            $file = $_FILES['file'];
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../task_file/' . date('Y-m-d', time()) . '/';
            $filePath = $path . time() . '.xlsx';

            if (empty($file['tmp_name'])) {
                throw new \Exception('上传文件不能为空');
            }
            $tmp = explode('.', $file['name']);
            $suffix = array_pop($tmp);
            if ('xlsx' !== $suffix) {
                throw new \Exception('上传文件无法识别，请使用模版');
            }
            if ($file['size'] > 5097152) {
                throw new \Exception('上传文件不能超过5MB');
            }

            //创建目录失败
            if (!file_exists($path) && !mkdir($path, 0777, true)) {
                throw new \Exception('创建文件夹失敗');
            } else if (!is_writeable($path)) {
                throw new \Exception('文件夹不可写');
            }
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception('复制文件失敗');
            }

            $model = new DeliveryOrderTask();
            $post = $this->request->post();
            $post['DeliveryOrderTask']['file_path'] = $filePath;
            $post['DeliveryOrderTask']['type'] = $post['type'];
            $post['DeliveryOrderTask']['settlement_dimension'] = !empty($post['settlement_dimension']) ? $post['settlement_dimension'] : '';
            $post['DeliveryOrderTask']['order_type'] = !empty($post['order_type']) ? $post['order_type'] : '';
            $post['DeliveryOrderTask']['apply_time'] = date('Y-m-d H:i:s', time());
            $post['DeliveryOrderTask']['apply_username'] = \Yii::$app->user->getIdentity()['username'];
            unset($post['type']);
            if ($model->load($post) && $model->save()) {
                $return['status'] = 1;
                $return['msg'] = '文件已经成功上传，请稍后转至任务列表查看进度。';
            } else {
                $return['msg'] = Utility::arrayToString($model->getErrors());
            }
        } catch (\Exception $e) {
            $return['msg'] = $e->getMessage();
        }
        exit(Json::encode($return));

    }

    /**
     * Updates an existing DeliveryOrderTask model.
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
     * Deletes an existing DeliveryOrderTask model.
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
     * Finds the DeliveryOrderTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DeliveryOrderTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeliveryOrderTask::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Updates an existing DeliveryOrderTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAjaxReRun()
    {
        $return = [
            'status' => 0,
            'msg' => '',
        ];
        $post = $this->request->post();
        $id = $post['id'];
        DeliveryOrderTask::reRun($id);
        $return['status'] = 1;
        $return['msg'] = '';
        exit(Json::encode($return));

    }
}

<?php

namespace backend\modules\finance\controllers;

use common\components\Utility;
use common\models\LogisticCompanyCheckBill;
use common\models\LogisticCompanyCheckBillDetail;
use common\models\LogisticCompanyCheckBillSearch;
use Psy\Util\Json;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LogisticCompanyCheckBillController implements the CRUD actions for LogisticCompanyCheckBill model.
 */
class LogisticCompanyCheckBillController extends Controller
{

    /**
     * Lists all LogisticCompanyCheckBill models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LogisticCompanyCheckBillSearch();
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
     * Lists all DeliveryOrder models.
     *
     * @return string
     */
    public function actionExportData()
    {
        $params = $this->request->queryParams;
        $searchModel = new LogisticCompanyCheckBillSearch();
        $searchModel->exportData($params);
    }

    /**
     * Displays a single LogisticCompanyCheckBill model.
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
     * Creates a new LogisticCompanyCheckBill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LogisticCompanyCheckBill();

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
     * Updates an existing LogisticCompanyCheckBill model.
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
            $model->load($post);
            $model->update_username = \Yii::$app->user->getIdentity()['username'];
            $model->update_time = date('Y-m-d H:i:s', time());
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LogisticCompanyCheckBill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        LogisticCompanyCheckBillDetail::deleteAll(['logistic_company_check_bill_no' => $model->logistic_company_check_bill_no]);
        $this->findModel($id)->delete();

        \Yii::$app->session->setFlash('success', '对账单删除成功!');
        return $this->redirect(['index']);
    }

    /**
     * Finds the LogisticCompanyCheckBill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return LogisticCompanyCheckBill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticCompanyCheckBill::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * 批量导入许可证编号模板下载
     */
    public function actionDownloadTemplate()
    {
        $excelUrl = "/www/wwwroot/jd_cityvan/backend/batch_upload_template/template2.xlsx";
//        $excelUrl = "/Users/hanxiao/jd_cityvan_git/backend/batch_upload_template/template2.xlsx";
        $filename = "批量导入快递信息模板.xlsx";
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($excelUrl));
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($excelUrl);
        exit;
    }

    /**
     *
     */
    public function actionAjaxBatchUpdate()
    {
        ini_set("max_execution_time", -1);

        $return = [
            'status' => 0,
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => '',
        ];
        // 文件校验
        try {
            if (!isset($_FILES['file'])) {
                throw new \Exception('上传文件不能为空');
            }
            $file = $_FILES['file'];
            if (empty($file['tmp_name'])) {
                throw new \Exception('上传文件不能为空');
            }
            $tmp = explode('.', $file['name']);
            $suffix = array_pop($tmp);
            if ('xlsx' !== $suffix) {
                throw new \Exception('上传文件无法识别，请使用模版');
            }
            if ($file['size'] > 2097152) {
                throw new \Exception('上传文件不能超过2MB');
            }
            $excelData = Utility::getExcelDataNew($file['tmp_name']);
            if (empty($excelData)) {
                throw new \Exception('上传文件无内容');
            }
        } catch (\Exception $e) {
            $return['errorList'] = $e->getMessage();
            exit(Json::encode($return));
        }
// 导入
        if (empty($excelData)) {
            $return['errorList'] = '数据为空';
            exit(Json::encode($return));
        }
        if (count($excelData) >= 5000) {
            $return['errorList'] = '数据量太大，不能超过5000条。';
            exit(Json::encode($return));
        }
        $return = LogisticCompanyCheckBillDetail::batchUpdate($excelData, \Yii::$app->user->getIdentity()['username']);
        $return['status'] = 1;
        $return['errorList'] = Utility::arrayToString($return['errorList']);

        echo Json::encode($return);
        exit;
    }

    public function actionPrint($id)
    {
        return $this->render('print', [
            'model' => $this->findModel($id),
        ]);
    }
}

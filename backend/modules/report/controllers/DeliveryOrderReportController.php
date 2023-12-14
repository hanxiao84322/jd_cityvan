<?php

namespace backend\modules\report\controllers;

use backend\models\Institution;
use common\components\Utility;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderSearch;
use yii\data\Pagination;
use yii\web\Controller;

/**
 * Default controller for the `report` module
 */
class DeliveryOrderReportController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionSendReceiveTimely()
    {

        $params = $this->request->queryParams;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchSendReceiveTimely($params);
        return $this->render('send-receive-timely', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionSendReceiveTimelyItems()
    {

        $params = $this->request->queryParams;
        $type = $params['type'];
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchSendReceiveTimelyItem($type);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        switch ($type) {
            case '1': //无揽收
                $typeName = '无揽收';
                break;
            case '2': //无运输
                $typeName = '无运输';
                break;
            case '3': //超时揽收
                $typeName = '超时揽收';
                break;
            case '4': //超时运输
                $typeName = '超时运输';
                break;
            default :
                $typeName = '';
                break;
        }
        return $this->render('send-receive-timely-items', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'typeName' => $typeName,
            'type' => $type,
        ]);
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionSendReceiveTimelyItemsExport()
    {

        $params = $this->request->queryParams;
        $type = $params['type'];
        switch ($type) {
            case '1': //无揽收
                $typeName = '无揽收';
                break;
            case '2': //无运输
                $typeName = '无运输';
                break;
            case '3': //超时揽收
                $typeName = '超时揽收';
                break;
            case '4': //超时运输
                $typeName = '超时运输';
                break;
            default :
                $typeName = '';
                break;
        }
        $searchModel = new DeliveryOrderSearch();
        $searchModel->searchSendReceiveTimelyItemExport($type, $typeName);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionTransportWarning()
    {
        $params = $this->request->queryParams;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchTransportWarning($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('transport-warning', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionTransportWarningItems()
    {

        $params = $this->request->queryParams;
        $type = $params['type'];
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchTransportWarningItem($type);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        switch ($type) {
            case '1': //运输即将超时
                $typeName = '运输即将超时';
                break;
            case '2': //超时运输结束
                $typeName = '超时运输结束';
                break;
            case '3': //无运输结束
                $typeName = '无运输结束';
                break;
            case '4': //超时配送中
                $typeName = '超时配送中';
                break;
            case '5': //无配送中
                $typeName = '无配送中';
                break;
            default :
                $typeName = '';
                break;
        }
        return $this->render('transport-warning-items', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'typeName' => $typeName,
            'type' => $type,
        ]);
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionTransportWarningItemsExport()
    {

        $params = $this->request->queryParams;
        $type = $params['type'];
        switch ($type) {
            case '1': //无揽收
                $typeName = '无揽收';
                break;
            case '2': //无运输
                $typeName = '无运输';
                break;
            case '3': //超时揽收
                $typeName = '超时揽收';
                break;
            case '4': //超时运输
                $typeName = '超时运输';
                break;
            default :
                $typeName = '';
                break;
        }
        $searchModel = new DeliveryOrderSearch();
        $searchModel->searchTransportWarningItemExport( $type, $typeName);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionFinalStatusWarning()
    {
        $params = $this->request->queryParams;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchFinalStatusWarning($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('final-status-warning', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionFinalStatusWarningExport()
    {
        $institutionId = \Yii::$app->user->getIdentity()['institution_id'];
        $params = $this->request->queryParams;
        $institutionRes = Institution::findOne($institutionId);
        $level = $institutionRes->level;
        if ($level != Institution::LEVEL_PARENT) {
            $params['DeliveryOrderSearch']['institution_id'] = $institutionId;
        }
        $searchModel = new DeliveryOrderSearch();
        $searchModel->searchFinalStatusWarningExport($params);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionUpdateStatus($id)
    {
        $model = DeliveryOrder::findOne($id);
        if ($this->request->isPost) {
            $post = $this->request->post();
            $model->load($post);

            $model->update_name = date('Y-m-d H:i:s', time());
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', '更新状态成功!');
                return $this->redirect(['/report/delivery-order-report/final-status-warning']);
            } else {
                \Yii::$app->session->setFlash('error', '更新状态失败，原因：' . Utility::arrayToString($model->getErrors()) . '!');
                return $this->redirect(['create']);
            }

        }
        return $this->render('update-status', [
            'model' => $model,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionOverdue()
    {
        $params = $this->request->queryParams;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchOverdue($params);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('overdue', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'create_month' => $searchModel->create_month,
            'warehouse_code' => $searchModel->warehouse_code,
            'logistic_id' => $searchModel->logistic_id,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionOverdueItems()
    {

        $params = $this->request->queryParams;
        $type = $params['type'];
        $createMonth = $params['create_month'];
        $warehouseCode = $params['warehouse_code'];
        $logisticId = $params['logistic_id'];
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchOverdueItems($type, $createMonth, $warehouseCode, $logisticId);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        switch ($type) {
            case '1': //运输即将超时
                $typeName = '滞留2天以内';
                break;
            case '2': //超时运输结束
                $typeName = '滞留2-3天';
                break;
            case '3': //无运输结束
                $typeName = '滞留3-5天';
                break;
            case '4': //超时配送中
                $typeName = '滞留5-7天';
                break;
            case '5': //超时配送中
                $typeName = '滞留7-10天';
                break;
            case '6': //超时配送中
                $typeName = '滞留10天以上';
                break;
            default :
                $typeName = '';
                break;
        }
        return $this->render('overdue-items', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'typeName' => $typeName,
            'type' => $type,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionRejectionWaitInWarehouse()
    {
        $params = $this->request->queryParams;
        $params['DeliveryOrderSearch']['status'] = DeliveryOrder::STATUS_REJECT;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->search($params, 1);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('rejection-wait-in-warehouse', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

    public function actionWaitSettlementWarning()
    {
        $params = $this->request->queryParams;
        $params['DeliveryOrderSearch']['is_logistic_company_settle'] = DeliveryOrder::NOT;
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->searchWaitSettlementWarning($params, 1);
        $pages = new Pagination(
            [
                'totalCount' => isset($dataProvider->totalCount) ? $dataProvider->totalCount : 0,
                'pageSize' => $searchModel->page_size,
            ]
        );
        return $this->render('wait-settlement-warning', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pages' => $pages,
        ]);
    }

}

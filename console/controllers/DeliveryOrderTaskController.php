<?php

namespace console\controllers;

use backend\models\BelongCity;
use backend\models\BelongCityStaff;
use common\components\Utility;
use common\models\Customer;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderTask;
use common\models\LogisticCompanyCheckBill;
use common\models\LogisticCompanyCheckBillDetail;
use yii\console\Controller;
use yii\helpers\Json;

class DeliveryOrderTaskController extends Controller
{
    /**
     * ./yii delivery-order-task/run
     */
    public function actionRun()
    {
        ini_set('memory_limit', '256M');
        $ret = [
            'success' => 0,
            'msg' => '',
            'return' => []
        ];
        $taskList = DeliveryOrderTask::find()->where(['status' => DeliveryOrderTask::STATUS_WAIT_UPDATE])->asArray()->all();
        if (empty($taskList)) {
            echo "没有待处理的数据。";
            exit;
        }
        foreach ($taskList as $task) {
            try {
                $taskId = $task['id'];
                $taskModel = DeliveryOrderTask::findOne($taskId);
                $taskModel->status = DeliveryOrderTask::STATUS_UPDATING;
                $taskModel->start_time = date('Y-m-d H:i:s', time());
                if (!$taskModel->save()) {
                    throw new \Exception(Utility::arrayToString($taskModel->getErrors()));
                }

                $errMsg = '任务ID：' . $taskId;
                if (!file_exists($task['file_path']) || !is_readable($task['file_path'])) {
                    throw new \Exception($errMsg . '文件不存在或者不可读');
                }

//                if (count($excelData) >= 50000) {
//                    throw new \Exception($errMsg . '数据量太大，不能超过50000条');
//                }
                echo "文件验证通过，开始批量导入\r\n";
                if ($task['type'] == DeliveryOrderTask::TYPE_ORDER) {
                    $excelData = Utility::getExcelDataNew($task['file_path']);
                    if (empty($excelData)) {
                        throw new \Exception($errMsg . '数据为空');
                    }
                    $return = DeliveryOrder::batchUpdate($excelData, 'system');
                } elseif ($task['type'] == DeliveryOrderTask::TYPE_LOGISTIC_COMPANY_CHECK_BILL) {
                    $excelData = Utility::getExcelDataNewNew($task['file_path']);
                    if (empty($excelData)) {
                        throw new \Exception($errMsg . '数据为空');
                    }
                    $return = LogisticCompanyCheckBillDetail::batchUpdate($excelData, $task['order_type'],'system');
                }
                $return['errorList'] = !empty($return['errorList']) ? join("|", $return['errorList']) : '';
                $ret['success'] = 1;
                $ret['return'] = $return;
            } catch (\Exception $e) {
                $ret['msg'] = $e->getMessage();
            }
            $taskModel = DeliveryOrderTask::findOne($taskId);
            $taskModel->status = DeliveryOrderTask::STATUS_UPDATED;
            $taskModel->end_time = date('Y-m-d H:i:s', time());
            $taskModel->result = Json::encode($ret);
            if (!$taskModel->save()) {
                echo "更新任务数据失败。" . Utility::arrayToString($taskModel->getErrors());
                exit;
            }
        }
        echo "finish";
    }

    /**
     * ./yii delivery-order-task/import
     *
     */
    public function actionImport()
    {
        $ret = [
            'success' => 0,
            'msg' => '',
            'return' => []
        ];

       try {
           $type = 2;
            $filePath = './1.xlsx';
            $excelData = Utility::getExcelDataNewNew($filePath);
            print_r($excelData);exit;
            $orderDataList = array_chunk($excelData, 1000);
            foreach ($orderDataList as $key => $orderData) {
                if ($type == DeliveryOrderTask::TYPE_ORDER) {
                    $return = DeliveryOrder::batchUpdate($excelData, 'system');
                } elseif ($type == DeliveryOrderTask::TYPE_LOGISTIC_COMPANY_CHECK_BILL) {
                    $return = LogisticCompanyCheckBillDetail::batchUpdate($excelData, 'system');
                }
                echo "第" . $key . "批，导入结果：" . json_encode($return, JSON_UNESCAPED_UNICODE) . "\r\n";
                sleep(2);
            }
        } catch (\Exception $e) {
            echo $e->getMessage()  . "\r\n";;
        }
        echo "finish";
    }

}


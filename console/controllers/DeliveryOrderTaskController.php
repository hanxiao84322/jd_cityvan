<?php

namespace console\controllers;

use backend\models\BelongCity;
use backend\models\BelongCityStaff;
use common\components\Utility;
use common\models\Customer;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderTask;
use yii\console\Controller;
use yii\helpers\Json;

class DeliveryOrderTaskController extends Controller
{
    /**
     * ./yii delivery-order-task/run
     */
    public function actionRun()
    {
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
                $errMsg = '任务ID：' . $taskId;
                if (!file_exists($task['file_path']) || !is_readable($task['file_path'])) {
                    throw new \Exception($errMsg . '文件不存在或者不可读');
                }
                $excelData = Utility::getExcelDataNew($task['file_path']);
                if (empty($excelData)) {
                    throw new \Exception($errMsg . '文件为空');
                }
                if (empty($excelData)) {
                    throw new \Exception($errMsg . '数据为空');
                }
                if (count($excelData) >= 50000) {
                    throw new \Exception($errMsg . '数据量太大，不能超过50000条');
                }
                $taskModel = DeliveryOrderTask::findOne($taskId);
                $taskModel->status = DeliveryOrderTask::STATUS_UPDATING;
                $taskModel->start_time = date('Y-m-d H:i:s', time());
                if (!$taskModel->save()) {
                    throw new \Exception(Utility::arrayToString($taskModel->getErrors()));
                }
                $return = DeliveryOrder::batchUpdate($excelData, 'system');
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
            $filePath = './3.xlsx';
            $excelData = Utility::getExcelDataNew($filePath);
            $orderDataList = array_chunk($excelData, 1000);
            foreach ($orderDataList as $key => $orderData) {
                $return = DeliveryOrder::batchUpdate($orderData, 'system');
                echo "第" . $key . "批，导入结果：" . json_encode($return, JSON_UNESCAPED_UNICODE) . "\r\n";
                sleep(2);
            }
        } catch (\Exception $e) {
            echo $e->getMessage()  . "\r\n";;
        }
        echo "finish";
    }

}


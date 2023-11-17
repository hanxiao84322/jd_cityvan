<?php

namespace console\controllers;

use backend\models\UserBackend;
use common\components\Utility;
use common\models\CustomerServiceDailyEfficiency;
use common\models\WorkOrder;
use yii\console\Controller;

class WorkOrderController extends Controller
{
    /**
     * ./yii work-order/sync
     */
    public function actionSync()
    {
        try {
            $filePath = './work_order/0.xlsx';
            $excelData = Utility::getExcelDataNew($filePath);
            $orderDataList = array_chunk($excelData, 1000);
            foreach ($orderDataList as $key => $orderData) {
                $return = WorkOrder::batchUpdate($orderData, 'system');
                echo "第" . $key . "批，导入结果：" . json_encode($return, JSON_UNESCAPED_UNICODE) . "\r\n";
                sleep(2);
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . "\r\n";
        }
        echo "finish";
    }

    /**
     * ./yii work-order/daily-efficiency-run '2023-02-04 00:00:00' '2023-02-04 23:59:59' 'test_customer_service'
     *
     * @param string $start
     * @param string $end
     * @param string $username
     * @throws \yii\db\Exception
     */
    public function actionDailyEfficiencyRun($start = '', $end = '', $username = '')
    {
        $sql = 'SELECT username,type, name FROM ' . UserBackend::tableName() . '  WHERE status = 1 AND type <> 1';
        if (!empty($username)) {
            $sql .= " AND username = '" . $username . "' ";
        }
        echo "sql:" . $sql . "\r\n";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有要执行的数据";
            exit;
        }

        $start = empty($start) ? date('Y-m-d 00:00:00', time()) : $start;
        $end = empty($end) ? date('Y-m-d 23:59:59', time()) : $end;

        foreach ($result as $item) {
            try {
                $workOrderSql = "SELECT assign_username, status, count(*) as create_num, sum(case when status = 4 then 1 else 0 end) as finished_num FROM `work_order` where create_time >= '" . $start . "' and create_time <= '" . $end . "'";
                $serviceUsername = $item['username'];
                $serviceName = $item['name'];

                if ($item['type'] == 2) {
                    $workOrderSql .= " and assign_username = '" . $serviceUsername . "'";
                }
                if ($item['type'] == 3) {
                    $workOrderSql .= " and operate_username = '" . $serviceUsername . "'";
                }
                echo "workOrderSql:" . $workOrderSql . "\r\n";
                $workOrderResult = \Yii::$app->db->createCommand($workOrderSql)->queryOne();
                $currentDay = date('Y-m-d', strtotime($start));

                if (!empty($workOrderResult)) {
                    $createNum = empty($workOrderResult['create_num']) ? 0 : $workOrderResult['create_num'];
                    $finishedNum = empty($workOrderResult['finished_num']) ? 0 : $workOrderResult['finished_num'];
                    $finishedRate = $createNum <> 0 ? round($workOrderResult['finished_num'] / $workOrderResult['create_num'], 2) : 0.00;
                    $model = CustomerServiceDailyEfficiency::findOne(['username' => $serviceUsername, 'date' => $currentDay]);
                    if (!$model) {
                        $model = new CustomerServiceDailyEfficiency();
                    }
                    $model->date = $currentDay;
                    $model->username = $serviceUsername;
                    $model->name = $serviceName;
                    $model->type = UserBackend::getTypeName($item['type']);
                    $model->work_order_create_num = $createNum;
                    $model->work_order_deal_num = $createNum - $finishedNum;
                    $model->work_order_finished_num = $finishedNum;
                    $model->work_order_not_finished_num = $createNum - $finishedNum;
                    $model->work_order_finished_rate = $finishedRate;
                    if (!$model->save()) {
                        throw new \Exception(Utility::arrayToString($model->getErrors()));
                    }
                }
                echo "日期：" . $currentDay . "用户名：" . $serviceUsername . ",类型：" . UserBackend::getTypeName($item['type']) . "创建成功！\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
            echo "finished";
        }
    }
}

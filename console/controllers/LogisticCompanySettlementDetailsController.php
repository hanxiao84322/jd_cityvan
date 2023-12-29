<?php

namespace console\controllers;

use backend\models\CustomerAreaDeliveryFee;
use backend\models\Institution;
use common\components\Utility;
use common\models\Cnarea;
use common\models\Customer;
use common\models\CustomerSettlementOrderDetail;
use common\models\DeliveryOrder;
use common\models\LogisticCompanyFeeRules;
use common\models\LogisticCompanySettlementOrderDetail;
use yii\base\ExitException;
use yii\console\Controller;

class LogisticCompanySettlementDetailsController extends Controller
{
    /**
     * ./yii logistic-company-settlement-details/run '' '' '' ''
     *
     * @param $logisticNo
     * @param $logisticId
     * @param $startTime
     * @param $endTime
     */
    public function actionRun($startTime = '', $endTime = '', $logisticId = '', $logisticNo = '')
    {
        $sql = "SELECT DISTINCT logistic_id FROM " . LogisticCompanyFeeRules::tableName() . "   where type = " . LogisticCompanyFeeRules::TYPE_LOGISTIC . "  ";
        if (!empty($logisticId)) {
            $sql .= " AND logistic_id =  " . $logisticId . "  ";
        }
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        if (empty($result)) {
            echo "没有需要处理的快递公司。";
            exit;
        }
        echo "有:" . count($result) . "个快递公司需要处理\r\n";

        foreach ($result as $item) {
            try {
                $return = [
                    'successCount' => 0,
                    'errorCount' => 0,
                    'errorList' => [],
                ];

                if (empty($startTime)) {
                    $startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
                }
                if (empty($endTime)) {
                    $endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));
                }
                    $deliveryOrderSql = "SELECT * FROM delivery_order where create_time > '2023-10-01' AND create_time >= '" . $startTime . "' AND create_time <= '" . $endTime . "' and logistic_id = '" . $item['logistic_id'] . "' AND is_logistic_company_settle = 0 ";
                if (!empty($logisticNo)) {
                    $deliveryOrderSql .= " AND logistic_no = '" . $logisticNo . "' ";
                }

                echo "deliveryOrderSql:" . $deliveryOrderSql . "\r\n";
                $deliveryOrderResult = \Yii::$app->db->createCommand($deliveryOrderSql)->queryAll();
                if (empty($deliveryOrderResult)) {
                    throw new \Exception("快递公司ID：" . $item['logistic_id'] . "没有需要处理的运单。");
                }
                echo "快递公司ID：" . $logisticId . "有:" . count($deliveryOrderResult) . "条运单需要处理\r\n";
                $processes = count($deliveryOrderResult)/20000; // 每个子进程跑 20000 条数据，需要创建的子进程数量
                if ($processes < 1) {
                    $processes = 2;
                }
                $chunks = array_chunk($deliveryOrderResult, ceil(count($deliveryOrderResult) / $processes)); // 将大数组拆分成多个小数组

                // 创建指定数量的子进程
                for ($i = 0; $i < $processes; $i++) {
                    try {
                        $pid = pcntl_fork();
                        if ($pid == -1) {
                            throw new \Exception('can not create sub processes');
                        } elseif ($pid) {
                            $tempFiles[$pid] = "temp_file_$pid.txt";
                        } else {
                            \Yii::$app->db->close(); // Close the connection if opened
                            \Yii::$app->db->open();  // Reopen the connection
                            $tempFile = "temp_file_" . getmypid() . ".txt";
                            // 打开临时文件用于写入
                            $tempHandle = fopen($tempFile, 'w');
                            // 子进程代码
                            echo "sub process data count:" . count($chunks[$i]) . "\r\n";
                            $result = $this->processChunk($chunks[$i]); // 执行任务并将结果存储在对应的索引位置
                            fwrite($tempHandle, json_encode($result) . PHP_EOL);
                            // 关闭文件句柄并结束子进程
                            fclose($tempHandle);
                            \Yii::$app->db->close();
                            sleep(2);
                            exit(); // 子进程执行完任务后退出
                        }
                    } catch (\Exception $e) {
                        $ret['msg'] = $e->getMessage();
                    }
                }
                // 等待子进程完成，并获取结果
                for ($i = 1; $i <= $processes; $i++) {
                    pcntl_wait($status);
                    echo "sub process {$i} is finish job\r\n";
                }
                $results = [];
                if (!empty($tempFiles)) {
                    foreach ($tempFiles as $tempFile) {
                        $results[] = file_get_contents($tempFile);
                        unlink($tempFile); // 删除临时文件
                    }
                }
                $resList = [];
                if (!empty($results)) {
                    foreach ($results as $re) {
                        $reArr = json_decode($re, true);
                        if (is_array($reArr)) {
                            $resList = array_merge($resList, $reArr);
                        } else {
                            var_dump($reArr);
                        }
                    }
                }
                if (!empty($resList)) {
                    foreach ($resList as $v) {
                        if (!$v['status']) {
                            $return['errorCount']++;
                            $return['errorList'][] = $v['errMsg'];
                        } else {
                            $return['successCount']++;
                        }
                    }
                }
                if ($return['errorCount'] == 0) {
                    sleep(10);
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
    }

    /**
     * ./yii logistic-company-settlement-details/jd-run '' '' '' '' ''
     *
     * @param $logisticNo
     * @param $orderNo
     * @param $warehouseCode
     * @param $province
     * @param $startTime
     * @param $endTime
     */
    public function actionJdRun($startTime = '', $endTime = '', $warehouseCode = '', $province = '', $logisticNo = '', $orderNo = '')
    {
        $sql = "SELECT warehouse_code, province FROM " . LogisticCompanyFeeRules::tableName() . " WHERE  type = " . LogisticCompanyFeeRules::TYPE_WAREHOUSE . "  ";
        if (!empty($warehouseCode)) {
            $sql .= " AND warehouse_code =  '" . $warehouseCode . "'  ";
        }
        if (!empty($province)) {
            $sql .= " AND province =  '" . $province . "'  ";
        }
        $sql .= "  GROUP BY warehouse_code, province ";
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        if (empty($result)) {
            echo "没有需要处理的仓库编码。";
            exit;
        }
        echo "有:" . count($result) . "个仓库需要处理\r\n";

        foreach ($result as $item) {
            try {
                $return = [
                    'successCount' => 0,
                    'errorCount' => 0,
                    'errorList' => [],
                ];
                if (empty($startTime)) {
                    $startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
                }
                if (empty($endTime)) {
                    $endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));
                }
                $deliveryOrderSql = "SELECT shipping_weight_rep, shipping_weight, order_weight, order_weight_rep, logistic_no, order_no, warehouse_code, logistic_id,finish_time, province, city, district FROM delivery_order where create_time > '2023-10-01' AND create_time >= '" . $startTime . "' AND create_time <= '" . $endTime . "' and warehouse_code = '" . $item['warehouse_code'] . "' AND province ='" . $item['province'] . "' AND  is_logistic_company_settle = 0 ";
                if (!empty($logisticNo)) {
                    $deliveryOrderSql .= " AND logistic_no = '" . $logisticNo . "' ";
                } else {
                    if (!empty($orderNo)) {
                        $deliveryOrderSql .= " AND order_no = '" . $orderNo . "' ";
                    }
                }

                echo "deliveryOrderSql:" . $deliveryOrderSql . "\r\n";
                $deliveryOrderResult = \Yii::$app->db->createCommand($deliveryOrderSql)->queryAll();
                if (empty($deliveryOrderResult)) {
                    throw new \Exception("仓库编码：" . $item['warehouse_code'] . "没有需要处理的订单。");
                }
                echo "仓库编码：" . $item['warehouse_code'] . "，省：" . $item['province'] .  "有:" . count($deliveryOrderResult) . "条运单需要处理\r\n";

                $processes = count($deliveryOrderResult)/20000; // 每个子进程跑 20000 条数据，需要创建的子进程数量
                if ($processes < 1) {
                    $processes = 2;
                }
                $chunks = array_chunk($deliveryOrderResult, ceil(count($deliveryOrderResult) / $processes)); // 将大数组拆分成多个小数组

                // 创建指定数量的子进程
                for ($i = 0; $i < $processes; $i++) {
                    try {
                        $pid = pcntl_fork();
                        if ($pid == -1) {
                            throw new \Exception('can not create sub processes');
                        } elseif ($pid) {
                            $tempFiles[$pid] = "temp_file_$pid.txt";
                        } else {
                            \Yii::$app->db->close(); // Close the connection if opened
                            \Yii::$app->db->open();  // Reopen the connection
                            $tempFile = "temp_file_" . getmypid() . ".txt";
                            // 打开临时文件用于写入
                            $tempHandle = fopen($tempFile, 'w');
                            // 子进程代码
                            echo "sub process data count:" . count($chunks[$i]) . "\r\n";
                            $result = $this->processChunk($chunks[$i], 'jd'); // 执行任务并将结果存储在对应的索引位置
                            fwrite($tempHandle, json_encode($result) . PHP_EOL);
                            // 关闭文件句柄并结束子进程
                            fclose($tempHandle);
                            \Yii::$app->db->close();
                            sleep(2);
                            exit(); // 子进程执行完任务后退出
                        }
                    } catch (\Exception $e) {
                        $ret['msg'] = $e->getMessage();
                    }
                }
                // 等待子进程完成，并获取结果
                for ($i = 1; $i <= $processes; $i++) {
                    pcntl_wait($status);
                    echo "sub process {$i} is finish job\r\n";
                }
                $results = [];
                if (!empty($tempFiles)) {
                    foreach ($tempFiles as $tempFile) {
                        $results[] = file_get_contents($tempFile);
                        unlink($tempFile); // 删除临时文件
                    }
                }
                $resList = [];
                if (!empty($results)) {
                    foreach ($results as $re) {
                        $reArr = json_decode($re, true);
                        if (is_array($reArr)) {
                            $resList = array_merge($resList, $reArr);
                        } else {
                            var_dump($reArr);
                        }
                    }
                }
                if (!empty($resList)) {
                    foreach ($resList as $v) {
                        if (!$v['status']) {
                            $return['errorCount']++;
                            $return['errorList'][] = $v['errMsg'];
                        } else {
                            $return['successCount']++;
                        }
                    }
                }
                if ($return['errorCount'] == 0) {
                    sleep(10);
                }

            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
            \Yii::$app->db->close(); // Close the connection if opened
            print_r($return);
            echo "finish";
        }
    }

    private function processChunk($chunk, $type = '')
    {
        // 这里是子进程执行的具体任务
        // 可以根据需求使用 Yii2 的组件和工具来处理任务，例如 ActiveRecord、队列组件等

        $result = [];
        try {
            // 执行任务
            foreach ($chunk as $data) {
                // 处理数据
                if ($type == 'jd') {
                    $processedRes = $this->jdProcessData($data);
                } else {
                    $processedRes = $this->processData($data);
                }
                $result[] = $processedRes;
            }
            return $result;
        } catch (ExitException $e) {
            // 处理异常
            echo "子进程发生错误：{$e->getMessage()}\r\n";
            return [];
        }
    }

    private function jdProcessData($deliveryOrder)
    {
        $return = [
            'status' => 0,
            'errMsg' => ''
        ];

        try {
            if (empty($deliveryOrder['shipping_weight_rep'])) {
                $jdWeight = $deliveryOrder['shipping_weight']; //京东结算取包裹重量
            } else {
                $jdWeight = $deliveryOrder['shipping_weight_rep']; //京东结算取包裹重量
            }
            if (empty($deliveryOrder['order_weight_rep'])) {
                $jdOrderWeight = $deliveryOrder['order_weight']; //京东结算取包裹重量
            } else {
                $jdOrderWeight = $deliveryOrder['order_weight_rep']; //京东结算取包裹重量
            }

            $itemOrderNo = $deliveryOrder['order_no'];

            $itemLogisticNo = $deliveryOrder['logistic_no'];
            $itemLogisticId = $deliveryOrder['logistic_id'];
            $itemWarehouseCode = $deliveryOrder['warehouse_code'];
            $finishedTime = $deliveryOrder['finish_time'];

            $province = $deliveryOrder['province'];
            $city = $deliveryOrder['city'];
            $district = $deliveryOrder['district'];

            $logisticCompanyFeeRulesSql = "SELECT * FROM " . LogisticCompanyFeeRules::tableName() . " where type = " . LogisticCompanyFeeRules::TYPE_WAREHOUSE . " AND warehouse_code = '" . $itemWarehouseCode . "' AND province = '" . $province . "' AND city = '" . $city . "' AND district = '" . $district . "'";
//                        echo "logisticCompanyFeeRulesSql:" . $logisticCompanyFeeRulesSql . "\r\n";
            $logisticCompanyFeeRulesResult = \Yii::$app->db->createCommand($logisticCompanyFeeRulesSql)->queryOne();
            if (empty($logisticCompanyFeeRulesResult)) {
                $logisticCompanyFeeRulesSql = "SELECT * FROM " . LogisticCompanyFeeRules::tableName() . "  where type = " . LogisticCompanyFeeRules::TYPE_WAREHOUSE . " AND warehouse_code = '" . $itemWarehouseCode . "' AND province = '" . $province . "' AND city = '" . $city . "'";
//                            echo "logisticCompanyFeeRulesSql:" . $logisticCompanyFeeRulesSql . "\r\n";
                $logisticCompanyFeeRulesResult = \Yii::$app->db->createCommand($logisticCompanyFeeRulesSql)->queryOne();
                if (empty($logisticCompanyFeeRulesResult)) {
                    if (empty($logisticCompanyFeeRulesResult)) {
                        $logisticCompanyFeeRulesSql = "SELECT * FROM " . LogisticCompanyFeeRules::tableName() . "  where type = " . LogisticCompanyFeeRules::TYPE_WAREHOUSE . " AND warehouse_code = '" . $itemWarehouseCode . "' AND province = '" . $province . "'";
//                                    echo "logisticCompanyFeeRulesSql:" . $logisticCompanyFeeRulesSql . "\r\n";
                        $logisticCompanyFeeRulesResult = \Yii::$app->db->createCommand($logisticCompanyFeeRulesSql)->queryOne();
                        if (empty($logisticCompanyFeeRulesResult)) {
                            throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . ",快递单号：" . $itemLogisticNo . "没有对应的运费规则");

                        }
                    }
                }
            }
            $fee = $logisticCompanyFeeRulesResult['price'];
            $orderFee = $logisticCompanyFeeRulesResult['price'];

            if ($jdWeight > $logisticCompanyFeeRulesResult['weight']) {
                $continueWeight = $jdWeight - $logisticCompanyFeeRulesResult['weight'];
                switch ($logisticCompanyFeeRulesResult['weight_round_rule']) {
                    case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_NOT_UP:
                        $continueWeight = intval($continueWeight);
                        break;
                    case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_HALF_UP:
                        $continueWeight = round($continueWeight);
                        break;
                    case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_UP:
                        $continueWeight = ceil($continueWeight);
                        break;
                    default:
                        $continueWeight = $continueWeight;
                        break;
                }
                $continueWeightRule = json_decode($logisticCompanyFeeRulesResult['continue_weight_rule'], true);
                if (empty($continueWeightRule)) {
                    throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . ",快递单号：" . $itemLogisticNo . "，重量：" . $jdWeight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueWeight . ",没有对应的续重规则");
                }
                $hasContinueWeightRule = false;
                foreach ($continueWeightRule as $value) {
                    if (($continueWeight >= $value[0] && $continueWeight < $value[1]) || ($continueWeight >= $value[0] && empty($value[1]))) {
                        $hasContinueWeightRule = true;
                        if ($logisticCompanyFeeRulesResult['continue_count_rule'] == LogisticCompanyFeeRules::CONTINUE_COUNT_RULE_ADDITION) {
                            $fee += $value[2];
                        } else {
                            $fee = $fee + ($continueWeight * $value[2]);
                        }
                    }
                }
                if (!$hasContinueWeightRule) {
                    throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . ",快递单号：" . $itemLogisticNo . "，重量：" . $jdWeight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueWeight . ",没有对应的续重规则");
                }
            }
            if ($jdOrderWeight > $logisticCompanyFeeRulesResult['weight']) {
                $continueOrderWeight = $jdOrderWeight - $logisticCompanyFeeRulesResult['weight'];
                switch ($logisticCompanyFeeRulesResult['weight_round_rule']) {
                    case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_NOT_UP:
                        $continueOrderWeight = intval($continueOrderWeight);
                        break;
                    case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_HALF_UP:
                        $continueOrderWeight = round($continueOrderWeight);
                        break;
                    case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_UP:
                        $continueOrderWeight = ceil($continueOrderWeight);
                        break;
                    default:
                        $continueOrderWeight = $continueOrderWeight;
                        break;
                }
                $continueWeightRule = json_decode($logisticCompanyFeeRulesResult['continue_weight_rule'], true);
                if (empty($continueWeightRule)) {
                    throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . ",订单号：" . $itemOrderNo . "，重量：" . $jdWeight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueOrderWeight . ",没有对应的续重规则");
                }
                $hasContinueWeightRule = false;
                foreach ($continueWeightRule as $value) {
                    if (($continueOrderWeight >= $value[0] && $continueOrderWeight < $value[1]) || ($continueOrderWeight >= $value[0] && empty($value[1]))) {
                        $hasContinueWeightRule = true;
                        if ($logisticCompanyFeeRulesResult['continue_count_rule'] == LogisticCompanyFeeRules::CONTINUE_COUNT_RULE_ADDITION) {
                            $orderFee += $value[2];
                        } else {
                            $orderFee = $orderFee + ($continueOrderWeight * $value[2]);
                        }
                    }
                }
                if (!$hasContinueWeightRule) {
                    throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district .  ",订单号：" . $itemOrderNo . "，重量：" . $jdWeight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueOrderWeight . ",没有对应的续重规则");
                }
            }

            $logisticCompanySettlementOrderDetailModel = LogisticCompanySettlementOrderDetail::findOne(['logistic_no' => $itemLogisticNo, 'warehouse_code' => $itemWarehouseCode]);
            if (!$logisticCompanySettlementOrderDetailModel) {
                $logisticCompanySettlementOrderDetailModel = new LogisticCompanySettlementOrderDetail();
                $logisticCompanySettlementOrderDetailModel->warehouse_code = $itemWarehouseCode;
                $logisticCompanySettlementOrderDetailModel->logistic_id = $itemLogisticId;
                $logisticCompanySettlementOrderDetailModel->logistic_no = $itemLogisticNo;
                $logisticCompanySettlementOrderDetailModel->province = $province;
                $logisticCompanySettlementOrderDetailModel->city = $city;
                $logisticCompanySettlementOrderDetailModel->district = $district;
                $logisticCompanySettlementOrderDetailModel->finish_time = $finishedTime;
                $logisticCompanySettlementOrderDetailModel->create_time = date('Y-m-d H:i:s', time());
            }
            $logisticCompanySettlementOrderDetailModel->order_no = $itemOrderNo;
            $splitAmount = LogisticCompanySettlementOrderDetail::getSplitAmount($orderFee, $itemOrderNo);
            $logisticCompanySettlementOrderDetailModel->jd_weight = $jdWeight;
            $logisticCompanySettlementOrderDetailModel->need_receipt_amount = $fee;
            $logisticCompanySettlementOrderDetailModel->jd_order_weight = $jdOrderWeight;
            $logisticCompanySettlementOrderDetailModel->order_need_receipt_amount = $orderFee;
            $logisticCompanySettlementOrderDetailModel->order_split_shipping_need_receipt_amount = $splitAmount;
            if (!$logisticCompanySettlementOrderDetailModel->save()) {
                throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . ",快递单号：" . $itemLogisticNo . "更新快递公司结算单明细失败，原因:" . Utility::arrayToString($logisticCompanySettlementOrderDetailModel->getErrors()));
            } else {
                DeliveryOrder::updateAll(['total_price' => $fee, 'split_total_price' => $splitAmount], ['logistic_no' => $itemLogisticNo]); //更新订单京东收取费用
            }
            $return['status'] = 1;
        } catch (\Exception $e) {
            $return['errMsg'] = $e->getMessage();
        }
        return $return;
    }


    private function processData($deliveryOrder)
    {
        $return = [
            'status' => 0,
            'errMsg' => ''
        ];
        try {
            $itemLogisticNo = $deliveryOrder['logistic_no'];
            $itemLogisticId = $deliveryOrder['logistic_id'];
            $itemWarehouseCode = $deliveryOrder['warehouse_code'];
            $weight = $deliveryOrder['post_office_weight']; //快递公司结算取物流重量
            $finishedTime = $deliveryOrder['finish_time'];
            $province = $deliveryOrder['province'];
            $city = $deliveryOrder['city'];
            $district = $deliveryOrder['district'];

            //快递公司计算运费开始
            $logisticCompanyFeeRulesSql = "SELECT * FROM " . LogisticCompanyFeeRules::tableName() . " where type = " . LogisticCompanyFeeRules::TYPE_LOGISTIC . " AND logistic_id = '" . $itemLogisticId . "' AND province = '" . $province . "' AND city = '" . $city . "' AND district = '" . $district . "'";
//                        echo "logisticCompanyFeeRulesSql:" . $logisticCompanyFeeRulesSql . "\r\n";
            $logisticCompanyFeeRulesResult = \Yii::$app->db->createCommand($logisticCompanyFeeRulesSql)->queryOne();
            if (empty($logisticCompanyFeeRulesResult)) {
                $logisticCompanyFeeRulesSql = "SELECT * FROM " . LogisticCompanyFeeRules::tableName() . "  where type = " . LogisticCompanyFeeRules::TYPE_LOGISTIC . " AND logistic_id = '" . $itemLogisticId . "' AND province = '" . $province . "' AND city = '" . $city . "'";
//                            echo "logisticCompanyFeeRulesSql:" . $logisticCompanyFeeRulesSql . "\r\n";
                $logisticCompanyFeeRulesResult = \Yii::$app->db->createCommand($logisticCompanyFeeRulesSql)->queryOne();
                if (empty($logisticCompanyFeeRulesResult)) {
                    if (empty($logisticCompanyFeeRulesResult)) {
                        $logisticCompanyFeeRulesSql = "SELECT * FROM " . LogisticCompanyFeeRules::tableName() . "  where type = " . LogisticCompanyFeeRules::TYPE_LOGISTIC . " AND logistic_id = '" . $itemLogisticId . "' AND province = '" . $province . "'";
//                                    echo "logisticCompanyFeeRulesSql:" . $logisticCompanyFeeRulesSql . "\r\n";
                        $logisticCompanyFeeRulesResult = \Yii::$app->db->createCommand($logisticCompanyFeeRulesSql)->queryOne();
                        if (empty($logisticCompanyFeeRulesResult)) {
                            throw new \Exception("快递公司ID：" . $itemLogisticId . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "没有对应的运费规则");

                        }
                    }
                }
            }
            switch ($logisticCompanyFeeRulesResult['weight_round_rule']) {
                case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_NOT_UP:
                    $weight = intval($weight);
                    break;
                case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_HALF_UP:
                    $weight = round($weight);
                    break;
                case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_UP:
                    $weight = ceil($weight);
                    break;
                case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_NOT:
                    $weight = $weight;
                    break;
                default:
                    break;
            }

            $fee = $logisticCompanyFeeRulesResult['price'];
            if ($weight > $logisticCompanyFeeRulesResult['weight']) {
                $continueWeight = $weight - $logisticCompanyFeeRulesResult['weight'];
                $continueWeightRule = json_decode($logisticCompanyFeeRulesResult['continue_weight_rule'], true);
                if (empty($continueWeightRule)) {
                    throw new \Exception("快递公司ID：" . $itemLogisticId . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "，重量：" . $weight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueWeight . ",没有对应的续重规则");
                }
                $hasContinueWeightRule = false;
                foreach ($continueWeightRule as $value) {
                    if (($continueWeight >= $value[0] && $continueWeight < $value[1]) || ($continueWeight >= $value[0] && empty($value[1]))) {
                        $hasContinueWeightRule = true;
                        $fee += $value[2];
                    }
                }

                if (!$hasContinueWeightRule) {
                    throw new \Exception("快递公司ID：" . $itemLogisticId . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "，重量：" . $weight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueWeight . ",没有对应的续重规则");
                }
            }

            $logisticCompanySettlementOrderDetailModel = LogisticCompanySettlementOrderDetail::findOne(['logistic_no' => $itemLogisticNo, 'logistic_id' => $itemLogisticId]);
            if (!$logisticCompanySettlementOrderDetailModel) {
                $logisticCompanySettlementOrderDetailModel = new LogisticCompanySettlementOrderDetail();
                $logisticCompanySettlementOrderDetailModel->logistic_id = $itemLogisticId;
                $logisticCompanySettlementOrderDetailModel->logistic_no = $itemLogisticNo;
                $logisticCompanySettlementOrderDetailModel->warehouse_code = $itemWarehouseCode;
                $logisticCompanySettlementOrderDetailModel->province = $province;
                $logisticCompanySettlementOrderDetailModel->city = $city;
                $logisticCompanySettlementOrderDetailModel->district = $district;
                $logisticCompanySettlementOrderDetailModel->finish_time = $finishedTime;
                $logisticCompanySettlementOrderDetailModel->create_time = date('Y-m-d H:i:s', time());
            }
            $logisticCompanySettlementOrderDetailModel->weight = $weight;
            $logisticCompanySettlementOrderDetailModel->need_pay_amount = $fee;
            if (!$logisticCompanySettlementOrderDetailModel->save()) {
                throw new \Exception("快递公司ID：" . $itemLogisticId . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "更新快递公司结算单明细失败，原因:" . Utility::arrayToString($logisticCompanySettlementOrderDetailModel->getErrors()));
            } else {
                DeliveryOrder::updateAll(['order_total_price' => $fee], ['logistic_no' => $itemLogisticNo]); //更新快递公司支付费用
            }
            $return['status'] = 1;
        } catch (\Exception $e) {
            $return['errMsg'] = $e->getMessage();
        }
        return $return;
    }
}
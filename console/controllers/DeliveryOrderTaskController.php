<?php

namespace console\controllers;

use common\components\Utility;
use common\models\Cnarea;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderTask;
use common\models\LogisticCompany;
use common\models\LogisticCompanyCheckBill;
use common\models\LogisticCompanyCheckBillDetail;
use common\models\LogisticCompanySettlementOrderDetail;
use common\models\LogisticCompanyTimeliness;
use common\models\Warehouse;
use yii\base\ExitException;
use yii\console\Controller;
use yii\helpers\Json;

class DeliveryOrderTaskController extends Controller
{


    /**
     * ./yii delivery-order-task/run
     */
    public function actionRun()
    {
        ini_set('memory_limit', '512M');
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

                if (!file_exists($task['file_path']) || !is_readable($task['file_path'])) {
                    throw new \Exception('任务 id：' . $taskId . '，文件不存在');
                }

//                if (count($excelData) >= 50000) {
//                    throw new \Exception($errMsg . '数据量太大，不能超过50000条');
//                }
                echo "file verify success, begin import\r\n";
                $return = [
                    'successCount' => 0,
                    'errorCount' => 0,
                    'errorList' => [],
                ];
                if ($task['type'] == DeliveryOrderTask::TYPE_ORDER) {
                    $excelData = Utility::getExcelDataNew($task['file_path']);
                    if (empty($excelData)) {
                        throw new \Exception('任务 id：' . $taskId . '，文件数据为空');
                    }
                    echo "data count:" . count($excelData) . "\r\n";
                    foreach ($excelData as $line => $item) {
                        try {
                            $address = Utility::changeToArea($item[11]);
                            $logisticNo = (string)$item[0];
                            $jdSendTime = $item[1];
                            $warehouseCode = (string)$item[2];
                            $orderNo = (string)$item[3];
                            $shippingNum = intval($item[4]);
                            $shippingNo = (string)$item[5];
                            $orderWeight = is_float($item[6]) ? $item[6] : (float)$item[6];
                            $orderWeightRep = is_float($item[7]) ? $item[7] : (float)$item[7];
                            $shippingWeight = is_float($item[8]) ? $item[8] : (float)$item[8];
                            $shippingWeightRep = is_float($item[9]) ? $item[9] : (float)$item[9];
                            $receiverName = (string)$item[10];
                            $receiverAddress = (string)$item[11];
                            $province = trim($address['province']);
                            $city = trim($address['city']);
                            $district = trim($address['district']);
                            $receiverPhone = (string)$item[12];
                            $postOfficeWeight = is_float($item[13]) ? $item[13] : (float)$item[13];
                            $logisticCompany = $item[14];

                            if (empty($orderNo)) {
                                continue;
                            }
                            $isUnusual = 0;
                            if (strlen($receiverName) > 40) {
                                $isUnusual = 1;
                            }
                            $logisticCompanyRes = LogisticCompany::find()->where(['company_name' => $logisticCompany])->asArray()->one();
                            if (empty($logisticCompanyRes)) {
                                throw new \Exception('不存在的物流名称:' . $logisticCompany);
                            }
                            $logisticId = $logisticCompanyRes['id'];

                            $warehouseRes = Warehouse::find()->where(['code' => $warehouseCode])->asArray()->one();
                            if (empty($warehouseRes)) {
                                throw new \Exception('不存在的仓库编码:' . $warehouseCode);
                            }
                            //快递公司不存在加一个
//                if (empty($logisticCompanyRes)) {
//                    $logisticCompanyModel = new LogisticCompany();
//                    $logisticCompanyModel->company_name = $logisticCompany;
//                    if (!$logisticCompanyModel->save()) {
//                        throw new \Exception(Utility::arrayToString($logisticCompanyModel->getErrors()));
//                    }
//                    $logisticId = $logisticCompanyModel->attributes['id'];
//                } else {
//                    $logisticId = $logisticCompanyRes['id'];
//                }

                            $addressSql = "SELECT `name` FROM `cnarea_2020` WHERE '" . $receiverAddress . "' LIKE CONCAT('%', `name`, '%') and level = 2 and (merger_name like \"%四川%\" or merger_name like \"%青海%\" or merger_name like \"%西藏%\" or merger_name like \"%甘肃%\")";

                            $addressResult = \Yii::$app->db->createCommand($addressSql)->queryOne();
                            if (!empty($addressResult)) {
                                $district = $addressResult['name'];

                                $city = Cnarea::getParentNameByName($district, Cnarea::LEVEL_THREE);
                                $province = Cnarea::getParentNameByName($city, Cnarea::LEVEL_TWO);
                                $timeliness = LogisticCompanyTimeliness::getTimelinessByDeliveryOrderInfo($warehouseCode, $logisticId, $province, $city, $district);
                            }
                            $deliveryOrderExists = DeliveryOrder::find()->where(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo])->exists();
                            if (!$deliveryOrderExists) {
                                $deliveryOrderModel = new DeliveryOrder();
                                $deliveryOrderModel->create_name = 'system';
                                $deliveryOrderModel->create_time = date('Y-m-d H:i:s', time());
                                $deliveryOrderModel->logistic_no = $logisticNo;
                                $deliveryOrderModel->shipping_no = $shippingNo;
                            } else {
                                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo]);
                                $deliveryOrderModel->update_name = 'system';
                                $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                            }
                            $deliveryOrderModel->warehouse_code = $warehouseCode;
                            $deliveryOrderModel->jd_send_time = $jdSendTime;
                            $deliveryOrderModel->timeliness = !empty($timeliness) ? $timeliness : 0;
                            $deliveryOrderModel->order_no = $orderNo;
                            $deliveryOrderModel->shipping_num = $shippingNum;
                            $deliveryOrderModel->order_weight = $orderWeight;
                            $deliveryOrderModel->shipping_weight = $shippingWeight;
                            $deliveryOrderModel->order_weight_rep = $orderWeightRep;
                            $deliveryOrderModel->shipping_weight_rep = $shippingWeightRep;
                            $deliveryOrderModel->post_office_weight = $postOfficeWeight;
                            $deliveryOrderModel->receiver_name = $receiverName;
                            $deliveryOrderModel->receiver_address = $receiverAddress;
                            $deliveryOrderModel->receiver_phone = $receiverPhone;
                            $deliveryOrderModel->province = $province;
                            $deliveryOrderModel->city = $city;
                            $deliveryOrderModel->district = $district;
                            $deliveryOrderModel->warehouse_code = $warehouseCode;
                            $deliveryOrderModel->logistic_id = $logisticId;
                            $deliveryOrderModel->is_unusual = $isUnusual;
                            if (!$deliveryOrderModel->save()) {
                                throw new \Exception(Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                            $return['successCount']++;
                            echo "第:" . $line . "行插入成功\r\n";
                        } catch (\Exception $e) {
                            $return['errorCount']++;
                            $errMsg = '第:' . $line . '行插入失败，原因:' . $e->getMessage();
                            echo $errMsg . "\r\n";
                            $return['errorList'][] = $errMsg;
                        }
                    }
                } elseif ($task['type'] == DeliveryOrderTask::TYPE_LOGISTIC_COMPANY_CHECK_BILL) {
                    try {
                        $excelData = Utility::getExcelDataNewNew($task['file_path']);
                        if (empty($excelData)) {
                            throw new \Exception('数据为空');
                        }
                        echo "data count:" . count($excelData) . "\r\n";
                        $tempOrderNo = 'TZF' . (string)time();
                        $processes = count($excelData)/20000; // 每个子进程跑 20000 条数据，需要创建的子进程数量
                        if ($processes < 1) {
                            $processes = 2;
                        }
                        $chunks = array_chunk($excelData, ceil(count($excelData) / $processes)); // 将大数组拆分成多个小数组
                        $tempFiles = []; // 用于存储临时文件名的数组
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
                                    $result = $this->processChunk($chunks[$i], $tempOrderNo); // 执行任务并将结果存储在对应的索引位置
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
                            $output = exec("./yii logistic-company-check-bill/run");
                            $return = json_decode($output, true);
                        }
                    } catch (\Exception $e) {
                        $ret['msg'] = $e->getMessage();
                    }
                }
                $return['errorList'] = !empty($return['errorList']) ? join("|", $return['errorList']) : '';
                $ret['success'] = 1;
                $ret['return'] = $return;
            } catch (\Exception $e) {
                $ret['msg'] = $e->getMessage();
            }
            \Yii::$app->db->close(); // Close the connection if opened
            \Yii::$app->db->open();  // Reopen the connection
            $taskModel = DeliveryOrderTask::findOne($taskId);
            $taskModel->status = DeliveryOrderTask::STATUS_UPDATED;
            $taskModel->end_time = date('Y-m-d H:i:s', time());
            $taskModel->result = Json::encode($ret);
            if (!$taskModel->save()) {
                echo "更新任务数据失败。" . Utility::arrayToString($taskModel->getErrors()) . "\r\n";
            }
            \Yii::$app->db->close(); // Close the connection if opened
        }
        echo "finish";
    }

    /**
     * ./yii delivery-order-task/run 8419127312301 2
     *
     * @param string $logisticNo
     * @param string $type
     * @throws \Exception
     */
    public function actionRowRun($logisticNo = '', $type = '')
    {
        ini_set('memory_limit', '512M');
        $ret = [
            'success' => 0,
            'msg' => '',
            'return' => []
        ];
        $return = [
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => [],
        ];
        if ($type == DeliveryOrderTask::TYPE_ORDER) {
            $excelData = DeliveryOrder::findAll(['logistic_no' => $logisticNo]);
            if (empty($excelData)) {
                throw new \Exception('data is empty');
            }

            echo "data count:" . count($excelData) . "\r\n";
            foreach ($excelData as $line => $item) {
                try {
                    $address = Utility::changeToArea($item[11]);
                    $logisticNo = (string)$item[0];
                    $jdSendTime = $item[1];
                    $warehouseCode = (string)$item[2];
                    $orderNo = (string)$item[3];
                    $shippingNum = intval($item[4]);
                    $shippingNo = (string)$item[5];
                    $orderWeight = is_float($item[6]) ? $item[6] : (float)$item[6];
                    $orderWeightRep = is_float($item[7]) ? $item[7] : (float)$item[7];
                    $shippingWeight = is_float($item[8]) ? $item[8] : (float)$item[8];
                    $shippingWeightRep = is_float($item[9]) ? $item[9] : (float)$item[9];
                    $receiverName = (string)$item[10];
                    $receiverAddress = (string)$item[11];
                    $province = trim($address['province']);
                    $city = trim($address['city']);
                    $district = trim($address['district']);
                    $receiverPhone = (string)$item[12];
                    $postOfficeWeight = is_float($item[13]) ? $item[13] : (float)$item[13];
                    $logisticCompany = $item[14];

                    if (empty($orderNo)) {
                        continue;
                    }
                    $isUnusual = 0;
                    if (strlen($receiverName) > 40) {
                        $isUnusual = 1;
                    }
                    $logisticCompanyRes = LogisticCompany::find()->where(['company_name' => $logisticCompany])->asArray()->one();
                    if (empty($logisticCompanyRes)) {
                        throw new \Exception('不存在的物流名称:' . $logisticCompany);
                    }
                    $logisticId = $logisticCompanyRes['id'];

                    $warehouseRes = Warehouse::find()->where(['code' => $warehouseCode])->asArray()->one();
                    if (empty($warehouseRes)) {
                        throw new \Exception('不存在的仓库编码:' . $warehouseCode);
                    }
                    //快递公司不存在加一个
//                if (empty($logisticCompanyRes)) {
//                    $logisticCompanyModel = new LogisticCompany();
//                    $logisticCompanyModel->company_name = $logisticCompany;
//                    if (!$logisticCompanyModel->save()) {
//                        throw new \Exception(Utility::arrayToString($logisticCompanyModel->getErrors()));
//                    }
//                    $logisticId = $logisticCompanyModel->attributes['id'];
//                } else {
//                    $logisticId = $logisticCompanyRes['id'];
//                }

                    $addressSql = "SELECT `name` FROM `cnarea_2020` WHERE '" . $receiverAddress . "' LIKE CONCAT('%', `name`, '%') and level = 2 and (merger_name like \"%四川%\" or merger_name like \"%青海%\" or merger_name like \"%西藏%\" or merger_name like \"%甘肃%\")";

                    $addressResult = \Yii::$app->db->createCommand($addressSql)->queryOne();
                    if (!empty($addressResult)) {
                        $district = $addressResult['name'];

                        $city = Cnarea::getParentNameByName($district, Cnarea::LEVEL_THREE);
                        $province = Cnarea::getParentNameByName($city, Cnarea::LEVEL_TWO);
                        $timeliness = LogisticCompanyTimeliness::getTimelinessByDeliveryOrderInfo($warehouseCode, $logisticId, $province, $city, $district);
                    }
                    $deliveryOrderExists = DeliveryOrder::find()->where(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo])->exists();
                    if (!$deliveryOrderExists) {
                        $deliveryOrderModel = new DeliveryOrder();
                        $deliveryOrderModel->create_name = 'system';
                        $deliveryOrderModel->create_time = date('Y-m-d H:i:s', time());
                        $deliveryOrderModel->logistic_no = $logisticNo;
                        $deliveryOrderModel->shipping_no = $shippingNo;
                    } else {
                        $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo]);
                        $deliveryOrderModel->update_name = 'system';
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                    }
                    $deliveryOrderModel->warehouse_code = $warehouseCode;
                    $deliveryOrderModel->jd_send_time = $jdSendTime;
                    $deliveryOrderModel->timeliness = !empty($timeliness) ? $timeliness : 0;
                    $deliveryOrderModel->order_no = $orderNo;
                    $deliveryOrderModel->shipping_num = $shippingNum;
                    $deliveryOrderModel->order_weight = $orderWeight;
                    $deliveryOrderModel->shipping_weight = $shippingWeight;
                    $deliveryOrderModel->order_weight_rep = $orderWeightRep;
                    $deliveryOrderModel->shipping_weight_rep = $shippingWeightRep;
                    $deliveryOrderModel->post_office_weight = $postOfficeWeight;
                    $deliveryOrderModel->receiver_name = $receiverName;
                    $deliveryOrderModel->receiver_address = $receiverAddress;
                    $deliveryOrderModel->receiver_phone = $receiverPhone;
                    $deliveryOrderModel->province = $province;
                    $deliveryOrderModel->city = $city;
                    $deliveryOrderModel->district = $district;
                    $deliveryOrderModel->warehouse_code = $warehouseCode;
                    $deliveryOrderModel->logistic_id = $logisticId;
                    $deliveryOrderModel->is_unusual = $isUnusual;
                    if (!$deliveryOrderModel->save()) {
                        throw new \Exception(Utility::arrayToString($deliveryOrderModel->getErrors()));
                    }
                    $return['successCount']++;
                    echo "line:" . $line . "insert success\r\n";
                } catch (\Exception $e) {
                    $return['errorCount']++;
                    $errMsg = 'line:' . $line . ' insert error，reason:' . $e->getMessage();
                    echo $errMsg . "\r\n";
                    $return['errorList'][] = $errMsg;
                }
            }
        } elseif ($type == DeliveryOrderTask::TYPE_LOGISTIC_COMPANY_CHECK_BILL) {
            $excelData = [
                0 => [
                    0 => 'cd',
                    1 => 16,
                    2 => '8419127312301',
                    3 => 2.95,
                    4 => 3.1]
            ];
            if (empty($excelData)) {
                throw new \Exception('data is empty');
            }
            echo "data count:" . count($excelData) . "\r\n";
            foreach ($excelData as $line => $item) {
                try {
                    $warehouseCode = (string)trim($item[0]);
                    $logisticId = (string)$item[1];
                    $logisticNo = (string)$item[2];
                    $orderWeight = is_float($item[3]) ? $item[3] : (float)$item[3];
                    $orderPrice = is_float($item[4]) ? $item[4] : (float)$item[4];
                    $note = '';
                    if (empty($warehouseCode) && empty($logisticId) && empty($logisticNo) && empty($orderWeight) && empty($orderPrice)) {
                        continue;
                    }
                    //                $logisticCompanyModel = LogisticCompany::findOne($logisticId);
                    //                if (!$logisticCompanyModel) {
                    //                    throw new \Exception('不存在的快递公司ID:' . $logisticId);
                    //                }
                    //                $warehouseModel = Warehouse::findOne(['code' => $warehouseCode]);
                    //                if (!$warehouseModel) {
                    //                    throw new \Exception('不存在的仓库编码:' . $warehouseCode);
                    //                }
                    $status = LogisticCompanyCheckBillDetail::STATUS_SAME;
                    $deliveryOrderModel = DeliveryOrder::findOne(['logistic_id' => $logisticId, 'warehouse_code' => $warehouseCode, 'logistic_no' => $logisticNo]);
                    if (empty($deliveryOrderModel)) {
                        $status = LogisticCompanyCheckBillDetail::STATUS_NOT_FOUND;
                    }

                    if (!in_array($deliveryOrderModel->status, [DeliveryOrder::STATUS_DELIVERED, DeliveryOrder::STATUS_REPLACE_DELIVERED, DeliveryOrder::STATUS_REJECT_IN_WAREHOUSE])) {
                        $note .= "订单状态是" . DeliveryOrder::getStatusName($deliveryOrderModel->status) . "未达到最终状态！\r\n";
                    }
                    $systemWeight = '';
                    $systemPrice = '';
                    $logisticCompanySettlementOrderDetailModel = LogisticCompanySettlementOrderDetail::findOne(['logistic_no' => $logisticNo]);
                    if (empty($logisticCompanySettlementOrderDetailModel)) {
                        $status = LogisticCompanyCheckBillDetail::STATUS_SYSTEM_NOT_SETTLEMENT;
                    } else {
                        $systemWeight = $logisticCompanySettlementOrderDetailModel->weight;
                        $systemPrice = $logisticCompanySettlementOrderDetailModel->need_pay_amount;
                        if ($systemWeight != $orderWeight) {
                            $status = LogisticCompanyCheckBillDetail::STATUS_WEIGHT_DIFF;
                            if ($systemPrice == $orderPrice) {
                                $status = LogisticCompanyCheckBillDetail::STATUS_SAME;
                            }
                        }
                        if ($systemPrice != $orderPrice) {
                            $status = LogisticCompanyCheckBillDetail::STATUS_WEIGHT_DIFF;
                        }
                    }

                    $logisticCompanyCheckBillDetailExists = LogisticCompanyCheckBillDetail::findOne(['logistic_no' => $logisticNo]);
                    if (!empty($logisticCompanyCheckBillDetailExists)) {
                        $status = LogisticCompanyCheckBillDetail::STATUS_EXISTS;
                        $note = '对账单号：' . $logisticCompanyCheckBillDetailExists->logistic_company_check_bill_no;
                    }
                    $logisticCompanyCheckBillDetailModel = new LogisticCompanyCheckBillDetail();
                    $logisticCompanyCheckBillDetailModel->warehouse_code = $warehouseCode;
                    $logisticCompanyCheckBillDetailModel->logistic_id = $logisticId;
                    $logisticCompanyCheckBillDetailModel->logistic_no = $logisticNo;
                    $logisticCompanyCheckBillDetailModel->weight = $orderWeight;
                    $logisticCompanyCheckBillDetailModel->price = $orderPrice;
                    $logisticCompanyCheckBillDetailModel->system_weight = $systemWeight;
                    $logisticCompanyCheckBillDetailModel->system_price = $systemPrice;
                    $logisticCompanyCheckBillDetailModel->status = $status;
                    $logisticCompanyCheckBillDetailModel->note = $note;
                    $logisticCompanyCheckBillDetailModel->create_username = 'system';
                    $logisticCompanyCheckBillDetailModel->create_time = date('Y-m-d H:i:s', time());
                    if (!$logisticCompanyCheckBillDetailModel->save()) {
                        throw new \Exception(Utility::arrayToString($logisticCompanyCheckBillDetailModel->getErrors()));
                    }
                    if (!isset($logisticIdCheckBillList[$logisticId][$warehouseCode]['total_count'])) {
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['total_count'] = 0;
                    }
                    $logisticIdCheckBillList[$logisticId][$warehouseCode]['total_count']++; //导入数量累加
                    if (!isset($logisticIdCheckBillList[$logisticId][$warehouseCode]['total_price'])) {
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['total_price'] = 0.00;
                    }
                    $logisticIdCheckBillList[$logisticId][$warehouseCode]['total_price'] += $orderPrice; //导入金额累加
                    if (!isset($logisticIdCheckBillList[$logisticId][$warehouseCode]['system_total_count'])) {
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['system_total_count'] = 0;
                    }
                    if (!isset($logisticIdCheckBillList[$logisticId][$warehouseCode]['system_total_price'])) {
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['system_total_price'] = 0.00;
                    }
                    if ($status == LogisticCompanyCheckBillDetail::STATUS_SAME) {
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['system_total_count']++; //系统数量累加
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['system_total_price'] += $systemPrice; //系统金额累加
                    }
                    if (!isset($logisticIdCheckBillList[$logisticId][$warehouseCode]['detailIdList'])) {
                        $logisticIdCheckBillList[$logisticId][$warehouseCode]['detailIdList'] = [];
                    }
                    $logisticIdCheckBillList[$logisticId][$warehouseCode]['detailIdList'][] = $logisticCompanyCheckBillDetailModel->id;
                    $return['successCount']++;
                    echo "line:" . $line . "insert success\r\n";
                } catch (\Exception $e) {
                    $return['errorCount']++;
                    $errMsg = 'line:' . $line . ' insert error，reason:' . $e->getMessage();
                    echo $errMsg . "\r\n";
                    $return['errorList'][] = $errMsg;
                }
            }
            if (empty($return['errorList'])) {
                if (!empty($logisticIdCheckBillList)) {
                    foreach ($logisticIdCheckBillList as $logisticId => $warehouseCheckBill) {
                        try {
                            foreach ($warehouseCheckBill as $warehouseCode => $checkBill) {
                                try {
                                    $logisticCompanyCheckBillModel = new LogisticCompanyCheckBill();
                                    $logisticCompanyCheckBillModel->logistic_company_check_bill_no = LogisticCompanyCheckBill::generateId();
                                    $logisticCompanyCheckBillModel->logistic_id = $logisticId;
                                    $logisticCompanyCheckBillModel->warehouse_code = $warehouseCode;
                                    $logisticCompanyCheckBillModel->date = date('Y-m-d', time());
                                    $logisticCompanyCheckBillModel->logistic_company_order_num = $checkBill['total_count'];
                                    $logisticCompanyCheckBillModel->system_order_num = $checkBill['system_total_count'];
                                    $logisticCompanyCheckBillModel->logistic_company_order_price = $checkBill['total_price'];
                                    $logisticCompanyCheckBillModel->system_order_price = $checkBill['system_total_price'];
                                    $logisticCompanyCheckBillModel->create_username = 'system';
                                    $logisticCompanyCheckBillModel->create_time = date('Y-m-d H:i:s', time());
                                    $logisticCompanyCheckBillModel->status = LogisticCompanyCheckBill::STATUS_NEW;
                                    $logisticCompanyCheckBillModel->type = 1;
                                    if (!$logisticCompanyCheckBillModel->save()) {
                                        throw new \Exception(Utility::arrayToString($logisticCompanyCheckBillModel->getErrors()));
                                    }
                                    LogisticCompanyCheckBillDetail::updateAll(['logistic_company_check_bill_no' => $logisticCompanyCheckBillModel->logistic_company_check_bill_no], ['in', 'id', $checkBill['detailIdList']]);
                                    $return['successCount']++;
                                    echo 'logistic id：' . $logisticId . "，warehouse code：" . $warehouseCode . " check bill insert success\r\n";
                                } catch (\Exception $e) {
                                    $return['errorCount']++;
                                    $errMsg = 'logistic id：' . $logisticId . "，warehouse code：" . $warehouseCode . ",check bill insert error,reason:" . $e->getMessage();
                                    $return['errorCount']++;
                                    $return['errorList'][] = $errMsg;
                                    echo $errMsg . "\r\n";
                                }
                            }
                        } catch (\Exception $e) {
                            $return['errorCount']++;
                            $errMsg = 'logistic id：' . $logisticId . ",check bill insert error,reason:" . $e->getMessage();
                            $return['errorList'][] = $errMsg;
                            echo $errMsg . "\r\n";
                        }

                    }

                }
            }
        }
        echo "finished";
    }

    /**
     * ./yii delivery-order-task/import
     *
     */
    public function actionImport()
    {
        date_default_timezone_set("Asia/Shanghai");
        ini_set('memory_limit', '512M');


        $ret = [
            'success' => 0,
            'msg' => '',
            'return' => []
        ];

        $type = 2;
        $filePath = './1.xlsx';

        echo "file verify success, begin import\r\n";
        $return = [
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => [],
        ];
        if ($type == DeliveryOrderTask::TYPE_ORDER) {
            $excelData = Utility::getExcelDataNew($filePath);
            if (empty($excelData)) {
                echo 'data is empty';
            }
            echo "data count:" . count($excelData) . "\r\n";
            foreach ($excelData as $line => $item) {
                try {
                    $address = Utility::changeToArea($item[11]);
                    $logisticNo = (string)$item[0];
                    $jdSendTime = $item[1];
                    $warehouseCode = (string)$item[2];
                    $orderNo = (string)$item[3];
                    $shippingNum = intval($item[4]);
                    $shippingNo = (string)$item[5];
                    $orderWeight = is_float($item[6]) ? $item[6] : (float)$item[6];
                    $orderWeightRep = is_float($item[7]) ? $item[7] : (float)$item[7];
                    $shippingWeight = is_float($item[8]) ? $item[8] : (float)$item[8];
                    $shippingWeightRep = is_float($item[9]) ? $item[9] : (float)$item[9];
                    $receiverName = (string)$item[10];
                    $receiverAddress = (string)$item[11];
                    $province = trim($address['province']);
                    $city = trim($address['city']);
                    $district = trim($address['district']);
                    $receiverPhone = (string)$item[12];
                    $postOfficeWeight = is_float($item[13]) ? $item[13] : (float)$item[13];
                    $logisticCompany = $item[14];

                    if (empty($orderNo)) {
                        continue;
                    }
                    $isUnusual = 0;
                    if (strlen($receiverName) > 40) {
                        $isUnusual = 1;
                    }
                    $logisticCompanyRes = LogisticCompany::find()->where(['company_name' => $logisticCompany])->asArray()->one();
                    if (empty($logisticCompanyRes)) {
                        throw new \Exception('不存在的物流名称:' . $logisticCompany);
                    }
                    $logisticId = $logisticCompanyRes['id'];

                    $warehouseRes = Warehouse::find()->where(['code' => $warehouseCode])->asArray()->one();
                    if (empty($warehouseRes)) {
                        throw new \Exception('不存在的仓库编码:' . $warehouseCode);
                    }
                    //快递公司不存在加一个
//                if (empty($logisticCompanyRes)) {
//                    $logisticCompanyModel = new LogisticCompany();
//                    $logisticCompanyModel->company_name = $logisticCompany;
//                    if (!$logisticCompanyModel->save()) {
//                        throw new \Exception(Utility::arrayToString($logisticCompanyModel->getErrors()));
//                    }
//                    $logisticId = $logisticCompanyModel->attributes['id'];
//                } else {
//                    $logisticId = $logisticCompanyRes['id'];
//                }

                    $addressSql = "SELECT `name` FROM `cnarea_2020` WHERE '" . $receiverAddress . "' LIKE CONCAT('%', `name`, '%') and level = 2 and (merger_name like \"%四川%\" or merger_name like \"%青海%\" or merger_name like \"%西藏%\" or merger_name like \"%甘肃%\")";

                    $addressResult = \Yii::$app->db->createCommand($addressSql)->queryOne();
                    if (!empty($addressResult)) {
                        $district = $addressResult['name'];

                        $city = Cnarea::getParentNameByName($district, Cnarea::LEVEL_THREE);
                        $province = Cnarea::getParentNameByName($city, Cnarea::LEVEL_TWO);
                        $timeliness = LogisticCompanyTimeliness::getTimelinessByDeliveryOrderInfo($warehouseCode, $logisticId, $province, $city, $district);
                    }
                    $deliveryOrderExists = DeliveryOrder::find()->where(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo])->exists();
                    if (!$deliveryOrderExists) {
                        $deliveryOrderModel = new DeliveryOrder();
                        $deliveryOrderModel->create_name = 'system';
                        $deliveryOrderModel->create_time = date('Y-m-d H:i:s', time());
                        $deliveryOrderModel->logistic_no = $logisticNo;
                        $deliveryOrderModel->shipping_no = $shippingNo;
                    } else {
                        $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo]);
                        $deliveryOrderModel->update_name = 'system';
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                    }
                    $deliveryOrderModel->warehouse_code = $warehouseCode;
                    $deliveryOrderModel->jd_send_time = $jdSendTime;
                    $deliveryOrderModel->timeliness = !empty($timeliness) ? $timeliness : 0;
                    $deliveryOrderModel->order_no = $orderNo;
                    $deliveryOrderModel->shipping_num = $shippingNum;
                    $deliveryOrderModel->order_weight = $orderWeight;
                    $deliveryOrderModel->shipping_weight = $shippingWeight;
                    $deliveryOrderModel->order_weight_rep = $orderWeightRep;
                    $deliveryOrderModel->shipping_weight_rep = $shippingWeightRep;
                    $deliveryOrderModel->post_office_weight = $postOfficeWeight;
                    $deliveryOrderModel->receiver_name = $receiverName;
                    $deliveryOrderModel->receiver_address = $receiverAddress;
                    $deliveryOrderModel->receiver_phone = $receiverPhone;
                    $deliveryOrderModel->province = $province;
                    $deliveryOrderModel->city = $city;
                    $deliveryOrderModel->district = $district;
                    $deliveryOrderModel->warehouse_code = $warehouseCode;
                    $deliveryOrderModel->logistic_id = $logisticId;
                    $deliveryOrderModel->is_unusual = $isUnusual;
                    if (!$deliveryOrderModel->save()) {
                        throw new \Exception(Utility::arrayToString($deliveryOrderModel->getErrors()));
                    }
                    $return['successCount']++;
                    echo "line:" . $line . "insert success\r\n";
                } catch (\Exception $e) {
                    $return['errorCount']++;
                    $errMsg = 'line:' . $line . ' insert error，reason:' . $e->getMessage();
                    echo $errMsg . "\r\n";
                    $return['errorList'][] = $errMsg;
                }
            }
        } elseif ($type == DeliveryOrderTask::TYPE_LOGISTIC_COMPANY_CHECK_BILL) {
            try {
                $excelData = Utility::getExcelDataNewNew($filePath);
                if (empty($excelData)) {
                    throw new \Exception('data is empty');
                }
                echo "data count:" . count($excelData) . "\r\n";
                $tempOrderNo = 'TZF' . (string)time();
                $result = []; // 存储最终结果的数组
                $processes = 2; // 需要创建的子进程数量
                $chunks = array_chunk($excelData, ceil(count($excelData) / $processes)); // 将大数组拆分成多个小数组
                $tempFiles = []; // 用于存储临时文件名的数组
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
                            $result = $this->processChunk($chunks[$i], $tempOrderNo); // 执行任务并将结果存储在对应的索引位置
                            fwrite($tempHandle, json_encode($result) . PHP_EOL);
                            // 关闭文件句柄并结束子进程
                            fclose($tempHandle);
                            \Yii::$app->db->close();
                            exit(); // 子进程执行完任务后退出
                        }

                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\r\n";
                    }
                }
                // 等待子进程完成，并获取结果
                for ($i = 1; $i <= $processes; $i++) {
                    pcntl_wait($status);
                    echo "sub process {$i} is finish job：" . print_r($result, true) . "\n";
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
                        $resList = array_merge($resList, $reArr);
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
                print_r($return);
                $output = exec("./yii logistic-company-check-bill/run");
                var_dump($output);exit;
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        echo "finish";
    }

    private function processChunk($chunk, $tempOrderNo)
    {
        // 这里是子进程执行的具体任务
        // 可以根据需求使用 Yii2 的组件和工具来处理任务，例如 ActiveRecord、队列组件等

        $result = [];
        try {
            // 执行任务
            foreach ($chunk as $data) {
                // 处理数据
                $processedRes = $this->processData($data, $tempOrderNo);
                $result[] = $processedRes;
            }
            return $result;
        } catch (ExitException $e) {
            // 处理异常
            echo "子进程发生错误：{$e->getMessage()}\r\n";
            return [];
        }
    }

    private function processData($item, $tempOrderNo)
    {
        $return = [
            'status' => 0,
            'errMsg' => ''
        ];
        // 根据具体需求处理数据的逻辑
        $warehouseCode = (string)trim($item[0]);
        $logisticId = (string)$item[1];
        $logisticNo = (string)$item[2];
        $orderWeight = is_float($item[3]) ? $item[3] : (float)$item[3];
        $orderPrice = is_float($item[4]) ? $item[4] : (float)$item[4];
        $note = '';
        try {

            if (empty($warehouseCode) || empty($logisticId) || empty($logisticNo) || empty($orderWeight) || empty($orderPrice)) {
                throw new \Exception('数据缺失，请检查！');
            }
            //                $logisticCompanyModel = LogisticCompany::findOne($logisticId);
            //                if (!$logisticCompanyModel) {
            //                    throw new \Exception('不存在的快递公司ID:' . $logisticId);
            //                }
            //                $warehouseModel = Warehouse::findOne(['code' => $warehouseCode]);
            //                if (!$warehouseModel) {
            //                    throw new \Exception('不存在的仓库编码:' . $warehouseCode);
            //                }
            $status = LogisticCompanyCheckBillDetail::STATUS_SAME;
            $deliveryOrderModel = DeliveryOrder::findOne(['logistic_id' => $logisticId, 'warehouse_code' => $warehouseCode, 'logistic_no' => $logisticNo]);
            if (empty($deliveryOrderModel)) {
                $status = LogisticCompanyCheckBillDetail::STATUS_NOT_FOUND;
            }

            if (!in_array($deliveryOrderModel->status, [DeliveryOrder::STATUS_DELIVERED, DeliveryOrder::STATUS_REPLACE_DELIVERED, DeliveryOrder::STATUS_REJECT_IN_WAREHOUSE])) {
                $note .= "订单状态是" . DeliveryOrder::getStatusName($deliveryOrderModel->status) . "未达到最终状态！\r\n";
            }
            $systemWeight = '';
            $systemPrice = '';
            $logisticCompanySettlementOrderDetailModel = LogisticCompanySettlementOrderDetail::findOne(['logistic_no' => $logisticNo]);
            if (empty($logisticCompanySettlementOrderDetailModel)) {
                $status = LogisticCompanyCheckBillDetail::STATUS_SYSTEM_NOT_SETTLEMENT;
            } else {
                $systemWeight = $logisticCompanySettlementOrderDetailModel->weight;
                $systemPrice = $logisticCompanySettlementOrderDetailModel->need_pay_amount;
                if ($systemWeight != $orderWeight) {
                    $status = LogisticCompanyCheckBillDetail::STATUS_WEIGHT_DIFF;
                    if ($systemPrice == $orderPrice) {
                        $status = LogisticCompanyCheckBillDetail::STATUS_SAME;
                    }
                }
                if ($systemPrice != $orderPrice) {
                    $status = LogisticCompanyCheckBillDetail::STATUS_WEIGHT_DIFF;
                }
            }

            $logisticCompanyCheckBillDetailExists = LogisticCompanyCheckBillDetail::findOne(['logistic_no' => $logisticNo]);
            if (!empty($logisticCompanyCheckBillDetailExists)) {
                $status = LogisticCompanyCheckBillDetail::STATUS_EXISTS;
                $note = '对账单号：' . $logisticCompanyCheckBillDetailExists->logistic_company_check_bill_no;
            }
            $logisticCompanyCheckBillDetailModel = new LogisticCompanyCheckBillDetail();
            $logisticCompanyCheckBillDetailModel->logistic_company_check_bill_no = $tempOrderNo;
            $logisticCompanyCheckBillDetailModel->warehouse_code = $warehouseCode;
            $logisticCompanyCheckBillDetailModel->logistic_id = $logisticId;
            $logisticCompanyCheckBillDetailModel->logistic_no = $logisticNo;
            $logisticCompanyCheckBillDetailModel->weight = $orderWeight;
            $logisticCompanyCheckBillDetailModel->price = $orderPrice;
            $logisticCompanyCheckBillDetailModel->system_weight = $systemWeight;
            $logisticCompanyCheckBillDetailModel->system_price = $systemPrice;
            $logisticCompanyCheckBillDetailModel->status = $status;
            $logisticCompanyCheckBillDetailModel->note = $note;
            $logisticCompanyCheckBillDetailModel->create_username = 'system';
            $logisticCompanyCheckBillDetailModel->create_time = date('Y-m-d H:i:s', time());
            if (!$logisticCompanyCheckBillDetailModel->save()) {
                throw new \Exception(Utility::arrayToString($logisticCompanyCheckBillDetailModel->getErrors()));
            }
            $return['status'] = 1;
        } catch (\Exception $e) {
            $return['errMsg'] =  '快递单号:' . $logisticNo . ',新建失败，原因:' . $e->getMessage();
        }
        return $return;
    }

}


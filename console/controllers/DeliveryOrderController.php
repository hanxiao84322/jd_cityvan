<?php

namespace console\controllers;

use common\components\TencentCloud;
use common\components\Utility;
use common\models\ActionLog;
use common\models\Cnarea;
use common\models\Customer;
use common\models\DeliveryImage;
use common\models\DeliveryOrder;
use common\models\ImportantCustomer;
use common\models\LogisticCompanyTimeliness;
use common\models\LogisticImage;
use common\models\WorkOrder;
use yii\console\Controller;

class DeliveryOrderController extends Controller
{

    /**
     * ./yii delivery-order/update-customer-id
     */
    public function actionUpdateCustomerId()
    {
        $institutionId = '';
        $deliveryOrderList = DeliveryOrder::find()->where(['customer_id' => '', 'is_batch_update' => DeliveryOrder::YES])->andWhere('sender_name is not null')->andWhere('sender_phone is not null')->andWhere('sender_address is not null')->andWhere('sender_company is not null')->asArray()->all();
        if (empty($deliveryOrderList)) {
            echo "没有要执行的数据";
            exit;
        }
        foreach ($deliveryOrderList as $v) {
            $customerRes = Customer::find()->where(['sender_company' => $v['sender_company'], 'sender_name' => $v['sender_name'], 'sender_phone' => $v['sender_phone'], 'sender_address' => $v['sender_address']])->asArray()->one();
            if (empty($customerRes)) {
                $customerModel = new Customer();
                $customerModel->sender_name = $v['sender_name'];
                $customerModel->sender_phone = $v['sender_phone'];
                $customerModel->sender_address = $v['sender_address'];
                $customerModel->sender_company = $v['sender_company'];
                $customerModel->status = Customer::STATUS_NORMAL;
                $customerModel->type = Customer::TYPE_SELF;
                $customerModel->create_name = 'system';
                $customerModel->create_time = date('Y-m-d H:i:s', time());
                if (!$customerModel->save()) {
                    echo "快递单号：" . $v['logistic_no'] . " 新增客户信息失败，原因：" . Utility::arrayToString($customerModel->getErrors());
                    exit;
                }
                $customerId = $customerModel->attributes['id'];
            } else {
                $customerId = $customerRes['id'];
                $institutionId = $customerRes['institution_id'];
            }
            $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $v['logistic_no']]);
            $deliveryOrderModel->customer_id = $customerId;
            $deliveryOrderModel->institution_id = $institutionId;
            if (!$deliveryOrderModel->save()) {
                echo "快递单号：" . $v['logistic_no'] . " 更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors());
                exit;
            }
        }

    }

    /**
     * ./yii delivery-order/update-institution-id
     */
    public function actionUpdateInstitutionId()
    {
        $deliveryOrderList = DeliveryOrder::find()->where(['is_batch_update' => DeliveryOrder::YES])->andWhere('institution_id is NULL')->andWhere('customer_id is not null')->asArray()->all();
        if (empty($deliveryOrderList)) {
            echo "没有要执行的数据";
            exit;
        }
        foreach ($deliveryOrderList as $v) {
            $customerRes = Customer::find()->where(['id' => $v['customer_id']])->asArray()->one();
            if (!empty($customerRes)) {
                $institutionId = $customerRes['institution_id'];
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $v['logistic_no']]);
                $deliveryOrderModel->institution_id = $institutionId;
                if (!$deliveryOrderModel->save()) {
                    echo "快递单号：" . $v['logistic_no'] . " 新增客户信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors());
                }
            }
        }
    }

    /**
     *
     * ./yii delivery-order/update-receiver-info-by-image 1 '' 'dws201' '2023-04-21 00:00:00' '2023-04-25 23:59:59' 4 1
     *
     * @param int $dryRun
     * @param string $logisticNo
     * @param string $deviceId
     * @param string $startTime
     * @param string $endTime
     * @param string $platform
     * @param int $isUpdate
     * @throws \yii\db\Exception
     */
    public function actionUpdateReceiverInfoByImage($dryRun = 1, $logisticNo = '', $deviceId = '', $startTime = '', $endTime = '', $platform = '', $isUpdate = 1)
    {
        $sql = 'SELECT o.* FROM delivery_order o left join logistic_image i on o.logistic_no = i.logistic_no where o.device_receiver_name is null and o.device_receiver_phone is null and o.create_time >= "2023-04-19 00:00:00"  and o.analysis_num < 2 and i.image_base64_str is not null  and is_need_analysis_ocr = 1 ';
        if (!empty($startTime)) {
            $sql .= "and  o.create_time >= '" . $startTime . "' ";
        } else {
            $time = date('Y-m-d 00:00:00', strtotime('-4 day'));
            $sql .= "and  o.create_time >= '" . $time . "' ";
        }
        if (!empty($endTime)) {
            $sql .= "and  o.create_time <= '" . $endTime . "' ";
        } else {
            $time = date('Y-m-d 23:59:59', time());
            $sql .= "and  o.create_time <= '" . $time . "' ";
        }
        if (!empty($deviceId)) {
            $sql .= "and  o.device_id = '" . $deviceId . "' ";
        }
        if (!empty($logisticNo)) {
            $sql .= 'AND o.logistic_no = "' . $logisticNo . '" ';
        }
        if (!empty($platform)) {
            switch ($platform) {
                case 1: //拼多多
                    $sql .= 'AND o.source = "拼多多" ';
                    break;
                case 2: //淘宝
                    $sql .= 'AND o.source = "淘宝" ';
                    break;
                case 3: //菜鸟
                    $sql .= 'AND o.source = "菜鸟" ';
                    break;
                case 4: //快团团
                    $sql .= 'AND o.source = "快团团" ';
                    break;
                default:
                    break;
            }
        }
        if (!$isUpdate) {
            $sql .= 'AND o.device_receiver_name = "" AND o.device_receiver_phone = "" ';
        }
        $sql .= " ORDER BY o.create_time DESC";
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                // if ((strlen($deliveryOrder['order_no']) !== 32)) { //拼多多
                //     if ($deliveryOrder['full_status'] === '信息完整') {
                //         continue;
                //     }
                // }
                $logisticNo = $deliveryOrder['logistic_no'];
                $logisticImage = LogisticImage::findOne(['logistic_no' => $logisticNo]);
                if (!$logisticImage) {
                    throw new \Exception("快递单号：" . $logisticNo . " 图片识别失败，没有找到图片信息。");
                }
                $image = $logisticImage->image_base64_str;
                // if (empty($image)) {
                //     throw new \Exception("快递单号：" . $logisticNo . " 图片识别失败，图片为空。");
                // }
                $orcRes = TencentCloud::analysisOcrData($image);
                sleep(1);
                if (!$orcRes['success']) {
                    throw new \Exception("快递单号：" . $logisticNo . " 图片识别失败，原因：" . $orcRes['msg']);
                }
                $name = $orcRes['data']['name'];
                $phone = $orcRes['data']['phone'];
                $text = $orcRes['data']['text'];
                $isSenderInfo = TencentCloud::isSenderInfo($name, $deliveryOrder['sender_name'], $phone, $deliveryOrder['sender_phone'], $orcRes['data']['extPhone']);

                if (!$dryRun) {
                    $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                    if (!$isSenderInfo) {
                        $deliveryOrderModel->device_receiver_name = $name;
                        $deliveryOrderModel->device_receiver_phone = $phone;
                    }
                    $deliveryOrderModel->analysis_num = $deliveryOrderModel->analysis_num + 1;
                    $deliveryOrderModel->update_name = 'system';
                    $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());

                    if (!$deliveryOrderModel->save()) {
                        throw new \Exception("快递单号：" . $logisticNo . "  更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                    }
                    $deliveryImageExists = DeliveryImage::find()->where(['logistic_no' => $logisticNo])->exists();
                    if (!$deliveryImageExists) {
                        $deliveryImageModel = new DeliveryImage();
                        $deliveryImageModel->create_time = date('Y-m-d H:i:s', time());
                    } else {
                        $deliveryImageModel = DeliveryImage::findOne(['logistic_no' => $logisticNo]);
                    }
                    //保存图片解析数据
                    $deliveryImageModel->logistic_no = $logisticNo;
                    $deliveryImageModel->image_data = $text;
                    if (!$deliveryImageModel->save()) {
                        throw new \Exception("快递单号：" . $logisticNo . "  新增运单图片解析数据失败，原因：" . Utility::arrayToString($deliveryImageModel->getErrors()));
                    }

                }
                echo "logisticNo:" . $logisticNo . ",name:" . $name . ",phone:" . $phone . "\r\n";
            } catch (\Exception $e) {
                ActionLog::log($logisticNo, 'UpdateReceiverInfoByImage-exception', '解析失败', $e->getMessage(), 'system');
                echo $e->getMessage() . "\r\n";
            }

        }
    }

    /**
     * ./yii delivery-order/fix-by-name 1 '周生'
     *
     * @param int $dryRun
     * @param string $deviceReceiverName
     * @throws \yii\db\Exception
     */
    public function actionFixByName($dryRun = 1, $deviceReceiverName = '')
    {
        $sql = 'SELECT o.* FROM delivery_order o left join logistic_image i on o.logistic_no = i.logistic_no where  i.image_base64_str is not null  and is_need_analysis_ocr = 1 AND device_receiver_name = "' . $deviceReceiverName . '"';
        $sql .= " ORDER BY o.create_time DESC";
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                $logisticNo = $deliveryOrder['logistic_no'];
                $logisticImage = LogisticImage::findOne(['logistic_no' => $logisticNo]);
                if (!$logisticImage) {
                    throw new \Exception("快递单号：" . $logisticNo . " 图片识别失败，没有找到图片信息。");
                }
                $image = $logisticImage->image_base64_str;

                $orcRes = TencentCloud::analysisOcrData($image);
                sleep(1);
                if (!$orcRes['success']) {
                    throw new \Exception("快递单号：" . $logisticNo . " 图片识别失败，原因：" . $orcRes['msg']);
                }
                $name = $orcRes['data']['name'];
                $phone = $orcRes['data']['phone'];
                $text = $orcRes['data']['text'];
                $isSenderInfo = TencentCloud::isSenderInfo($name, $deliveryOrder['sender_name'], $phone, $deliveryOrder['sender_phone'], $orcRes['data']['extPhone']);

                if (!$dryRun) {
                    $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                    if (!$isSenderInfo) {
                        $deliveryOrderModel->device_receiver_name = '';
                        $deliveryOrderModel->device_receiver_phone = '';
                    }
                    $deliveryOrderModel->analysis_num = $deliveryOrderModel->analysis_num + 1;
                    $deliveryOrderModel->update_name = 'system';
                    $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());

                    if (!$deliveryOrderModel->save()) {
                        throw new \Exception("快递单号：" . $logisticNo . "  更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                    }
                    $deliveryImageExists = DeliveryImage::find()->where(['logistic_no' => $logisticNo])->exists();
                    if (!$deliveryImageExists) {
                        $deliveryImageModel = new DeliveryImage();
                        $deliveryImageModel->create_time = date('Y-m-d H:i:s', time());
                    } else {
                        $deliveryImageModel = DeliveryImage::findOne(['logistic_no' => $logisticNo]);
                    }
                    //保存图片解析数据
                    $deliveryImageModel->logistic_no = $logisticNo;
                    $deliveryImageModel->image_data = $text;
                    if (!$deliveryImageModel->save()) {
                        throw new \Exception("快递单号：" . $logisticNo . "  新增运单图片解析数据失败，原因：" . Utility::arrayToString($deliveryImageModel->getErrors()));
                    }

                }
                echo "logisticNo:" . $logisticNo . ",name:" . $name . ",phone:" . $phone . "\r\n";
            } catch (\Exception $e) {
                ActionLog::log($logisticNo, 'UpdateReceiverInfoByImage-exception', '解析失败', $e->getMessage(), 'system');
                echo $e->getMessage() . "\r\n";
            }

        }

    }

    /**
     * ./yii delivery-order/test 20230824151925440
     * @param string $orderNo
     */
    public function actionTest($orderNo = '')
    {
        $deliveryNo = DeliveryOrder::GeneralDeliveryNo();
        echo $deliveryNo;
        exit;
        $orderSourceRes = DeliveryOrder::getSourceByOrderNo($orderNo);
        print_r($orderSourceRes);
    }

    /**
     *  ./yii delivery-order/batch-update-by-images
     */
    public function actionBatchUpdateByImages()
    {
        $folderPath = '2023-08-31'; // 替换为实际的文件夹路径

        // 获取文件夹中的图片文件列表
        $imageFiles = glob($folderPath . '/*.jpg');

        foreach ($imageFiles as $imageFile) {
            try {

                // 获取文件名（不包括扩展名）
                $filename = pathinfo($imageFile, PATHINFO_FILENAME);

                // 将图片文件转换为Base64字符串
                $imageData = base64_encode(file_get_contents($imageFile));
                $orcRes = TencentCloud::analysisOcrData($imageData);

                if (!$orcRes['success']) {
                    throw new \Exception("图片名：" . $filename . " 识别失败，原因：" . $orcRes['msg']);
                }
                $logisticNo = $filename;
                if (empty($logisticNo)) {
                    throw new \Exception("图片名：" . $filename . " 识别失败，原因：没有识别到运单号");
                }
                $logisticImageExist = LogisticImage::find()->where(['logistic_no' => $logisticNo])->exists();
                if ($logisticImageExist) {
                    $logisticImageModel = LogisticImage::findOne(['logistic_no' => $logisticNo]);
                } else {
                    $logisticImageModel = new LogisticImage();
                    $logisticImageModel->logistic_no = $logisticNo;
                }
                $logisticImageModel->device_id = 'DWS002';
                $logisticImageModel->image_base64_str = $imageData;
                $logisticImageModel->create_time = date('Y-m-d H:i:s', time());
                if (!$logisticImageModel->save()) {
                    throw new \Exception('保存运单图片数据失败，原因：' . Utility::arrayToString($logisticImageModel->getErrors()));
                }

                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                if (!empty($deliveryOrderModel)) {
                    $name = $orcRes['data']['name'];
                    $phone = $orcRes['data']['phone'];
                    $text = $orcRes['data']['text'];
                    $deliveryOrderModel->analysis_num = $deliveryOrderModel->analysis_num + 1;
                    $deliveryOrderModel->device_receiver_name = $name;
                    $deliveryOrderModel->device_receiver_phone = $phone;
                    //保存图片解析数据
                    $deliveryImageExists = DeliveryImage::find()->where(['logistic_no' => $logisticNo])->exists();
                    if ($deliveryImageExists) {
                        $deliveryImageModel = DeliveryImage::findOne(['logistic_no' => $logisticNo]);
                    } else {
                        $deliveryImageModel = new DeliveryImage();
                    }
                    $deliveryImageModel->logistic_no = $logisticNo;
                    $deliveryImageModel->image_data = $text;
                    if (!$deliveryImageModel->save()) {
                        throw new \Exception('保存运单图片解析数据失败，原因：' . Utility::arrayToString($deliveryImageModel->getErrors()));
                    }
                    if (!$deliveryOrderModel->save()) {
                        throw new \Exception(Utility::arrayToString($deliveryOrderModel->getErrors()));
                    }
                }
                sleep(1);
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
    }

    /**
     * ./yii delivery-order/check-retention
     *
     * @throws \yii\db\Exception
     */
    public function actionCheckRetention()
    {
        $sql = "SELECT 
logistic_no, ((CASE WHEN(
            TIMESTAMPDIFF(HOUR,send_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,send_time, NOW()) < 48 AND 
            transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) < 48 AND 
            transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) < 48 AND 
            delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) < 48 AND 
            (delivered_time IS NULL AND (replace_delivered_time IS NULL OR delivered_time IS NULL))
        ) THEN '1' ELSE '0' END)) AS `retention_two_days` , 
        ((CASE WHEN(
            TIMESTAMPDIFF(HOUR,send_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,send_time, NOW()) < 72 AND 
            transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) < 72 AND 
            transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) < 72 AND 
            delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) < 72 AND 
            (delivered_time IS NULL  AND (replace_delivered_time IS NULL OR delivered_time IS NULL))
        ) THEN '1' ELSE '0' END)) AS `retention_three_days` , 
        ((CASE WHEN(
            TIMESTAMPDIFF(HOUR,send_time, NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,send_time, NOW()) < 120 AND 
            transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) < 120 AND 
            transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) < 120 AND 
            delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) < 120 AND 
            (delivered_time IS NULL  AND (replace_delivered_time IS NULL OR delivered_time IS NULL))
        ) THEN '1' ELSE '0' END)) AS `retention_five_days` ,
        ((CASE WHEN(
            TIMESTAMPDIFF(HOUR,send_time, NOW()) >= 120 AND 
            transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transporting_time, NOW()) >= 120 AND 
            transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,transported_time, NOW()) >= 120 AND 
            delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,delivering_time, NOW()) >= 120 AND 
            (delivered_time IS NULL  AND (replace_delivered_time IS NULL OR delivered_time IS NULL))
        ) THEN '1' ELSE '0' END)) AS `retention_more_five_days` FROM `delivery_order` WHERE create_time >= '2023-10-01' AND status in (1,2,3,4);";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                DeliveryOrder::updateAll(['is_retention' => $deliveryOrder['retention_two_days'], 'is_serious_retention' => $deliveryOrder['retention_three_days'], 'is_overdue' => ($deliveryOrder['retention_five_days'] || $deliveryOrder['retention_more_five_days'])], ['logistic_no' => $deliveryOrder['logistic_no']]);
                //创建工单
                if ($deliveryOrder['overdue'] == 1) {
                    $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $deliveryOrder['logistic_no']]);
                    if (empty($deliveryOrderModel)) {
                        throw new \Exception('快递单号:' . $deliveryOrder['logistic_no'] . '不存在');
                    }
                    $workOrderModel = new WorkOrder();
                    $workOrderModel->logistic_no = $deliveryOrderModel->logistic_no;
                    $workOrderModel->order_no = $deliveryOrderModel->order_no;
                    $workOrderModel->warehouse_code = $deliveryOrderModel->warehouse_code;

                    $workOrderModel->receive_name = $deliveryOrderModel->receiver_name;
                    $workOrderModel->receive_phone = $deliveryOrderModel->receiver_phone;
                    $workOrderModel->receive_address = $deliveryOrderModel->receiver_address;
                    $workOrderModel->logistic_id = $deliveryOrderModel->logistic_id;
                    $workOrderModel->type = 7;
                    $workOrderModel->order_create_num = WorkOrder::getCreateNumByLogisticNo($deliveryOrderModel->logistic_no);
                    $importantCustomerModel = ImportantCustomer::findOne(['name' => $deliveryOrderModel->receiver_name, 'phone' => $deliveryOrderModel->receiver_phone]);
                    if (empty($importantCustomerModel)) {
                        $importantCustomerModel = new ImportantCustomer();
                        $importantCustomerModel->name = $deliveryOrderModel->receiver_name;
                        $importantCustomerModel->phone = $deliveryOrderModel->receiver_phone;
                        $importantCustomerModel->address = $deliveryOrderModel->receiver_address;
                        $importantCustomerModel->complaint_type = '滞留件';
                        $importantCustomerModel->work_order_num = WorkOrder::getCountByUsername($deliveryOrderModel->receiver_name);
                        $importantCustomerModel->level = ImportantCustomer::getLevelByCount($importantCustomerModel->work_order_num);
                        $importantCustomerModel->create_name = 'system';
                        $importantCustomerModel->create_time = date('Y-m-d H:i:s', time());
                        if (!$importantCustomerModel->save()) {
                            throw new \Exception('快递单号:' . $deliveryOrderModel->logistic_no . '创建重点客户失败，原因：' . Utility::arrayToString($importantCustomerModel->getErrors()));
                        }
                        echo "订单号：" . $deliveryOrder['logistic_no'] . "创建重点客户成功！" . "\r\n";
                    }
                    $workOrderModel->customer_attention_level = ImportantCustomer::getLevelByNameAndPhone($deliveryOrderModel->receiver_name, $deliveryOrderModel->receiver_phone);
                    $workOrderModel->system_create = 1;
                    $workOrderModel->work_order_no = WorkOrder::generateId();
                    $workOrderModel->status = WorkOrder::STATUS_WAIT_DEAL;
                    $workOrderModel->assign_username = 'system';
                    $workOrderModel->create_username = 'system';
                    $workOrderModel->create_time = date('Y-m-d H:i:s', time());
                    if (!$workOrderModel->save()) {
                        throw new \Exception('快递单号:' . $deliveryOrderModel->logistic_no . '创建工单失败，原因：' . Utility::arrayToString($workOrderModel->getErrors()));
                    }
                    echo "订单号：" . $deliveryOrder['logistic_no'] . "创建工单成功！" . "\r\n";

                }

                echo "订单号：" . $deliveryOrder['logistic_no'] . "更新状态成功" . json_encode($deliveryOrder) . "\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
        echo "finished";
    }

    /**
     * ./yii delivery-order/update-area 16 8458858189301
     *
     * @param string $logisticNo
     * @param string $logisticId
     * @throws \yii\db\Exception
     */
    public function actionUpdateArea($logisticId = '', $logisticNo = '')
    {
        $sql = "SELECT id,logistic_no, warehouse_code, logistic_id, province, city, district,receiver_address FROM `delivery_order` WHERE  create_time >= '2023-10-01' AND  province not in ('四川省', '青海省', '甘肃省', '西藏自治区');";
//        $sql = "SELECT id,logistic_no, warehouse_code, logistic_id, province, city, district,receiver_address FROM `delivery_order` WHERE create_time > '2023-10-01'";
        if (!empty($logisticId)) {
            $sql .= " AND logistic_id = '" . $logisticId . "' ";
        }
        if (!empty($logisticNo)) {
            $sql .= " AND logistic_no = '" . $logisticNo . "' ";
        }

        echo "sql:" . $sql . "\r\n";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                $addressSql = "SELECT `name` FROM `cnarea_2020` WHERE '" . $deliveryOrder['receiver_address'] . "' LIKE CONCAT('%', `name`, '%') and level = 2 and (merger_name like \"%四川%\" or merger_name like \"%青海%\" or merger_name like \"%西藏%\" or merger_name like \"%甘肃%\")";
                $addressResult = \Yii::$app->db->createCommand($addressSql)->queryOne();
                if (!empty($addressResult)) {
                    $district = $addressResult['name'];
                    $city = Cnarea::getParentNameByName($district);
                    $province = Cnarea::getParentNameByName($city);
                    DeliveryOrder::updateAll(['province' => $province, 'city' => $city, 'district' => $district], ['id' => $deliveryOrder['id']]);
                }

                echo $deliveryOrder['id'] . ":success\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
        echo "finished";
    }

    /**
     * ./yii delivery-order/update-timeliness '2023-12-02' '2023-12-03' '16'
     *
     * @param string $startTime
     * @param string $endTime
     * @param string $logisticId
     * @param string $logisticNo
     * @throws \yii\db\Exception
     */
    public function actionUpdateTimeliness($startTime = '', $endTime = '', $logisticId = '', $logisticNo = '')
    {
        $warehouseList = [];
        $logisticIdList = [];
        $logisticCompanyTimelinessSql = "SELECT * FROM logistic_company_timeliness WHERE status = "  . LogisticCompanyTimeliness::STATUS_NORMAL . "  ";
        $logisticCompanyTimelinessResult = \Yii::$app->db->createCommand($logisticCompanyTimelinessSql)->queryAll();
        foreach ($logisticCompanyTimelinessResult as $value) {
            $warehouseList[] = $value['warehouse_code'];
            $logisticIdList[] = $value['logistic_id'];
        }

        $warehouseList = array_unique($warehouseList);
        $logisticIdList = array_unique($logisticIdList);
        $warehouseStr = "'" . implode("','", $warehouseList) . "'";
        $logisticIdStr = "'" . implode("','", $logisticIdList) . "'";
        $sql = "SELECT logistic_no, warehouse_code, logistic_id, province, city, district FROM `delivery_order` WHERE  warehouse_code in (" . $warehouseStr . ") AND logistic_id in (" . $logisticIdStr . ")   ";


        if (!empty($logisticNo)) {
            $sql .= " AND logistic_no = '" . $logisticNo . "' ";
        } else {
            if (!empty($startTime)) {
                $sql .= " AND  create_time >= '" . $startTime . "' ";
            } else {
                $sql .= " AND  create_time >= ''2023-10-01 00:00:00'' ";
            }
            if (!empty($endTime)) {
                $sql .= " AND  create_time <= '" . $endTime . "' ";
            }
            if (!empty($logisticId)) {
                $sql .= " AND logistic_id = '" . $logisticId . "' ";
            }
        }
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {

                $logisticCompanyTimeliness = LogisticCompanyTimeliness::getTimelinessByDeliveryOrderInfo($deliveryOrder['warehouse_code'], $deliveryOrder['logistic_id'], $deliveryOrder['province'], $deliveryOrder['city'], $deliveryOrder['district']);
                if ($logisticCompanyTimeliness) {
                    DeliveryOrder::updateAll(['timeliness' => $logisticCompanyTimeliness], ['logistic_no' => $deliveryOrder['logistic_no']]);
                }

            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
        echo "finished";
    }

}


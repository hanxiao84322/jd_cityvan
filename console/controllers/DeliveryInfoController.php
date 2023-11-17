<?php

namespace console\controllers;


use common\components\EmsCloud;
use common\components\KdApi;
use common\components\Utility;
use common\components\ZtoCloud;
use common\models\ActionLog;
use common\models\DeliveryInfo;
use common\models\LogisticCompany;
use common\models\ZjsDeliveryInfo;
use yii\console\Controller;
use \common\models\DeliveryOrder;
use \common\components\ZjsCloud;
use yii\db\Exception;

class DeliveryInfoController extends Controller
{

    /**
     * ./yii delivery-info/update-data
     * @throws Exception
     */
    public function actionUpdateData()
    {
        $sql = 'SELECT logistic_no FROM `delivery_order` WHERE  (DATEDIFF(NOW(), `send_time`) > 20) AND (status not in (8,9,10)) ORDER BY `create_time` DESC';
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        if (empty($result)) {
            echo "没有要执行的数据";
            exit;
        }
        echo count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                $logisticNo = $deliveryOrder['logistic_no'];
                $deliveryInfoRes = ZjsCloud::getDeliveryInfo($logisticNo);
                if (!$deliveryInfoRes['success']) {
                    throw new \Exception("快递单号：" . $logisticNo . "获取物流轨迹失败，原因：" . $deliveryInfoRes['msg']);
                }
                $deliveryInfoRes = json_decode($deliveryInfoRes['data'], true);
                if ($deliveryInfoRes['description'] !== '成功!') {
                    throw new \Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：" . $deliveryInfoRes['description']);
                }
                if (isset($deliveryInfoRes['orders'][0]) && !empty($deliveryInfoRes['orders'][0])) {
                    $deliveryOrder = $deliveryInfoRes['orders'][0];
                    if ($logisticNo != $deliveryOrder['mailNo']) {
                        throw new \Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：快递单号不一致，解析出快递单号为：" . $deliveryOrder['mailNo']);
                    }
                    $deliverySteps = $deliveryOrder['steps'];
                    if (!empty($deliverySteps)) {
                        foreach ($deliverySteps as $deliveryStep) {
                            $operationDescribe = $deliveryStep['operationDescribe'];
                            $operationTime = $deliveryStep['operationTime'];
                            $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                            if (strpos($operationDescribe, '已取件') !== false) {
                                $status = DeliveryOrder::STATUS_RECEIVE;
                                $deliveryOrderModel->status = $status;
                                $deliveryOrderModel->receive_time = $operationTime;
                            } elseif (strpos($operationDescribe, '离开') !== false || (strpos($operationDescribe, '到达') !== false && (strpos($operationDescribe, '[新疆') === false) && (strpos($operationDescribe, '【新疆') === false) && (strpos($operationDescribe, '[乌鲁木齐') === false) && (strpos($operationDescribe, '【乌鲁木齐') === false))) {
                                $status = DeliveryOrder::STATUS_TRANSPORTING;
                                if ($deliveryOrderModel->transporting_time == '' || $deliveryOrderModel->transporting_time == null) {
                                    $deliveryOrderModel->transporting_time = $operationTime;
                                    $deliveryOrderModel->status = $status;
                                }
                            } elseif ((strpos($operationDescribe, '到达[新疆') !== false) || (strpos($operationDescribe, '到达【新疆') !== false) || (strpos($operationDescribe, '到达[乌鲁木齐') !== false) || (strpos($operationDescribe, '到达【乌鲁木齐') !== false)) {
                                $status = DeliveryOrder::STATUS_TRANSPORTED;
                                $deliveryOrderModel->status = $status;
                                $deliveryOrderModel->transported_time = $operationTime;
                            } elseif (strpos($operationDescribe, '货物已转发') !== false) {
                                $status = DeliveryOrder::STATUS_DELIVERING;
                                $deliveryOrderModel->status = $status;
                                $deliveryOrderModel->delivering_time = $operationTime;
                                $pattern = '/\[(.*?)\]/';
                                preg_match_all($pattern, $operationDescribe, $matches);
                                if (!empty($matches[1])) {
                                    if (isset($matches[1][0]) && !empty($matches[1][0])) {
                                        $secLogisticCompany = $matches[1][0];
                                        $logisticCompanyModel = LogisticCompany::findOne(['company_name' => $secLogisticCompany]);
                                        if (!$logisticCompanyModel) {
                                            $logisticCompanyModel = new LogisticCompany();
                                            $logisticCompanyModel->company_name = $secLogisticCompany;
                                            if (!$logisticCompanyModel->save()) {
                                                throw new Exception("快递单号：" . $logisticNo . "新增快递公司失败，原因：" . Utility::arrayToString($logisticCompanyModel->getErrors()));
                                            }
                                            $secLogisticId = $logisticCompanyModel->attributes['id'];
                                        } else {
                                            $secLogisticId = $logisticCompanyModel->id;
                                        }
                                        $deliveryOrderModel->sec_logistic_id = $secLogisticId;

                                    }
                                    if (isset($matches[1][1]) && !empty($matches[1][1])) {
                                        $secLogisticNo = $matches[1][1];
                                        $deliveryOrderModel->sec_logistic_no = $secLogisticNo;
                                    }
                                }
                            } elseif (strpos($operationDescribe, '签收') !== false) {
                                $status = DeliveryOrder::STATUS_DELIVERED;
                                $deliveryOrderModel->status = $status;
                                $deliveryOrderModel->delivered_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            } elseif (strpos($operationDescribe, '拒收') !== false) {
                                $status = DeliveryOrder::STATUS_REJECT;
                                $deliveryOrderModel->status = $status;
                                $deliveryOrderModel->reject_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            } elseif (strpos($operationDescribe, '丢失') !== false) {
                                $status = DeliveryOrder::STATUS_LOST;
                                $deliveryOrderModel->status = $status;
                                $deliveryOrderModel->lost_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            }
                            $deliveryOrderModel->latest_track_info = $operationDescribe;
                            if (!$deliveryOrderModel->save()) {
                                throw new \Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                            $deliveryInfoExist = DeliveryInfo::find()->where(['logistic_no' => $logisticNo, 'status' => $status, 'content' => $operationDescribe, 'update_time' => $operationTime])->exists();
                            if (!$deliveryInfoExist) {
                                $deliveryInfoModel = new DeliveryInfo();
                                $deliveryInfoModel->logistic_no = $logisticNo;
                                $deliveryInfoModel->status = $status;
                                $deliveryInfoModel->content = $operationDescribe;
                                $deliveryInfoModel->update_time = $operationTime;
                                if (!$deliveryInfoModel->save()) {
                                    throw new \Exception("快递单号：" . $logisticNo . "新增物流信息失败，原因：" . Utility::arrayToString($deliveryInfoModel->getErrors()));
                                }
                            }
                        }

                    } else {
                        throw new \Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：steps为空");
                    }

                } else {
                    throw new \Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：orders-0不存在");
                }
                echo "success" . "\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }

            sleep(1);
        }
        echo "finish" . "\r\n";
    }

    /**
     * ./yii delivery-info/test 'ZJS000361342741'
     */
    public function actionTest($logisticNo = '')
    {
        $operationDescribe = '您的快件已代签收【家人，已转隆宝邮政所13号到达】，如有疑问请电联快递员【才仁拉措，电话:16697085682】。连接美好，无处不在，感谢您使用中国邮政，期待再次为您服务。';
        if (strpos($operationDescribe, '到达') !== false && ((strpos($operationDescribe, '邮政所') !== false) || (strpos($operationDescribe, '揽投部') !== false)) && strpos($operationDescribe, '代签收') === false) {
            echo DeliveryOrder::STATUS_TRANSPORTED;
        } elseif (strpos($operationDescribe, '配送中') !== false || strpos($operationDescribe, '派送中') !== false) {
            echo DeliveryOrder::STATUS_DELIVERING;
        } elseif (((strpos($operationDescribe, '代签收') !== false || strpos($operationDescribe, '自提') !== false || strpos($operationDescribe, '驿站') !== false || strpos($operationDescribe, '村邮站') !== false) && strpos($operationDescribe, '退回') === false) || (strpos($operationDescribe, '签收') !== false && strpos($operationDescribe, '本人') === false && strpos($operationDescribe, '准备') === false)) {
            echo DeliveryOrder::STATUS_REPLACE_DELIVERED;
        } elseif (strpos($operationDescribe, '本人签收') !== false) {
            echo DeliveryOrder::STATUS_DELIVERED;
        } elseif (strpos($operationDescribe, '退回') !== false || strpos($operationDescribe, '退件') !== false || strpos($operationDescribe, '寄件人签收') !== false || strpos($operationDescribe, '成功退回至寄件人') !== false) {
            echo DeliveryOrder::STATUS_REJECT;
        }
        exit;
//        $deliveryInfoRes = DeliveryInfo::getContentAndTimeByLogisticNoAndStatus('ZJS001400428875', 'e8c21fa2aca0873e58328a799e54b612', DeliveryOrder::STATUS_DELIVERING);
//        var_dump($deliveryInfoRes);exit;

//        for ($i = 0; $i < 5; $i++) {
//            $numRed[] = rand(1, 35);
//        }
//        for ($i = 0; $i < 2; $i++) {
//            $numBlue[] = rand(1, 12);
//        }
//        sort($numRed);
//        sort($numBlue);
//        foreach ($numRed as $itemRed) {
//            echo $itemRed . ",";
//        }
//        echo "——";
//            foreach ($numBlue as $itemBlue) {
//                echo $itemBlue . ",";
//            }
//            echo "\r\n";
//        exit;
        // $sql = 'SELECT logistic_no, status, latest_track_info, delivered_time, finish_time, create_time FROM `delivery_order` WHERE status <> 8 and latest_track_info like "%签收%"';
        // $result = \Yii::$app->db->createCommand($sql)->queryAll();
        // foreach ($result as $item) {
        //     $zjsDeliveryInfoSql = 'SELECT * FROM `zjs_delivery_info` where mail_no = "' . $item['logistic_no'] . '" and `desc` like "%签收%" ';
        //     $zjsDeliveryInfoResult = \Yii::$app->db->createCommand($zjsDeliveryInfoSql)->queryOne();
        //     if (!empty($zjsDeliveryInfoResult)) {
        //         echo $zjsDeliveryInfoResult['time'] . "\r\n";
        //         $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $item['logistic_no']]);
        //         $deliveryOrderModel->delivered_time = $zjsDeliveryInfoResult['time'];
        //         $deliveryOrderModel->finish_time = $zjsDeliveryInfoResult['time'];
        //         $deliveryOrderModel->status = 8;
        //         if (!$deliveryOrderModel->save()) {
        //             echo Utility::arrayToString($deliveryOrderModel->getErrors());
        //         } else {
        //             echo "success\r\n";
        //         }
        //     }
        // }
        // exit;
//        $str = '货物已转发：[中运新疆圆通]  运单号: [YT2500385631309]';
//        $pattern = '/\[(.*?)\]/';
//        preg_match_all($pattern, $str, $matches);
//        print_r($matches);exit;
//        $logisticSubscribe = KdApi::getDeliveryInfo('ZJS008436866550');
//        $logisticSubscribe = ZjsCloud::getDeliveryInfo('ZJS001403341671');
        // $logisticSubscribe = ZjsCloud::getDeliveryInfo($logisticNo);
//         var_dump($logisticSubscribe);
        exit;
    }


    /**
     * ./yii delivery-info/fix-time
     *
     * @throws Exception
     */
    public function actionFixTime()
    {
        $sql = 'SELECT logistic_no, create_time, status, latest_track_info, transported_time FROM `delivery_order` where status = 6 and transported_time is null order by create_time desc';
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($result as $item) {
            $zjsDeliveryInfoSql = 'SELECT * FROM `zjs_delivery_info` where mail_no = "' . $item['logistic_no'] . '" and (`desc` like "%到达[新疆%" or `desc` like "%到达【新疆%" or `desc` like "%到达[乌鲁木齐%" or `desc` like "%到达【乌鲁木齐%")  ';
            $zjsDeliveryInfoResult = \Yii::$app->db->createCommand($zjsDeliveryInfoSql)->queryOne();
            if (!empty($zjsDeliveryInfoResult)) {
                echo $zjsDeliveryInfoResult['time'] . "\r\n";
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $item['logistic_no']]);
                $deliveryOrderModel->delivered_time = $zjsDeliveryInfoResult['time'];
                $deliveryOrderModel->finish_time = $zjsDeliveryInfoResult['time'];
                $deliveryOrderModel->status = 8;
                if (!$deliveryOrderModel->save()) {
                    echo Utility::arrayToString($deliveryOrderModel->getErrors());
                } else {
                    echo "success\r\n";
                }
            }
        }
        exit;
    }

    /**
     *
     * ./yii delivery-info/update 0 '2023-10-16 00:00:00' '2023-10-20 23:59:59' ''
     * @param int $dryRun
     * @param string $logisticNo
     * @param string $startTime
     * @param string $endTime
     * @throws Exception
     */
    public function actionUpdate($dryRun = 1, $startTime = '', $endTime = '', $logisticNo = '')
    {
        $fixSql = "SELECT send_time, logistic_no, logistic_id, status, latest_track_info, create_time FROM `delivery_order` WHERE create_time > '2023-10-01 00:00:00' and  status not in (5,6,7,8) ";

        if (!empty($logisticNo)) {
            $fixSql .= ' AND logistic_no = "' . $logisticNo . '" ';
        } else {
            if (!empty($startTime) && !empty($endTime)) {
                $fixSql .= " and create_time >= '" . $startTime . "' and  create_time <= '" . $endTime . "' ";
            }
        }
        $fixSql .= " order by create_time DESC ";
        echo "fixSql:" . $fixSql . "\r\n";
        $result = \Yii::$app->db->createCommand($fixSql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                $deliverySteps = [];
                $deliveryStepList = [];
                $logisticNo = $deliveryOrder['logistic_no'];
                $logisticId = $deliveryOrder['logistic_id'];
                if (in_array($logisticId, [3, 48089])) {
                    $deliveryInfoRes = KdApi::getDeliveryInfo($logisticNo, 'zhongtong'); //获取物流轨迹
                    if (!$deliveryInfoRes['success']) {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流轨迹失败，原因：" . $deliveryInfoRes['msg']);
                    }
                    if (empty($deliveryInfoRes['data'])) {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流轨迹失败，原因：轨迹信息为空！");
                    }
                    foreach ($deliveryInfoRes['data'] as $key => $datum) {
                        $deliverySteps[$key]['operationDescribe'] = $datum['context'];
                        $deliverySteps[$key]['operationTime'] = $datum['time'];
                    }
                } else {
                    $deliveryInfoRes = EmsCloud::getDeliveryInfo($logisticNo); //获取物流轨迹
                    if (empty($deliveryInfoRes['traces'])) {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流轨迹失败，原因：轨迹信息为空！");
                    }
                    foreach ($deliveryInfoRes['traces'] as $key => $value) {
                        $deliverySteps[$key]['operationDescribe'] = $value['remark'];
                        $deliverySteps[$key]['operationTime'] = $value['acceptTime'];
                    }
                }

                if (empty($deliverySteps)) {
                    throw new Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：轨迹信息为空！");
                }
                $deliveryStepList = Utility::order_date_array($deliverySteps, 'asc', 'operationTime');
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                if ($deliveryOrderModel->status == DeliveryOrder::STATUS_SYNC) {
                    if (isset($deliveryStepList[0]['operationDescribe']) && isset($deliveryStepList[0]['operationTime'])) {
                        $deliveryOrderModel->status = DeliveryOrder::STATUS_SEND;
                        $deliveryOrderModel->send_time = $deliveryStepList[0]['operationTime'];
                        $deliveryOrderModel->latest_track_info = $deliveryStepList[0]['operationDescribe'];
                        $deliveryOrderModel->latest_track_time = $deliveryStepList[0]['operationTime'];
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
//                        echo DeliveryOrder::STATUS_SEND . "——" . $deliveryStepList[0]['operationDescribe'] . $deliveryStepList[0]['operationTime'] . "\r\n";
                        if (!$dryRun) {
                            if (!$deliveryOrderModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                        }
                    } else {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流信息失败，原因：物流信息格式错误" . Utility::arrayToString($deliverySteps));
                    }
                }
                if ($deliveryOrderModel->status == DeliveryOrder::STATUS_SEND) {
                    if (isset($deliveryStepList[1]['operationDescribe']) && isset($deliveryStepList[1]['operationTime'])) {
                        $deliveryOrderModel->status = DeliveryOrder::STATUS_TRANSPORTING;
                        $deliveryOrderModel->transporting_time = $deliveryStepList[1]['operationTime'];
                        $deliveryOrderModel->latest_track_info = $deliveryStepList[1]['operationDescribe'];
                        $deliveryOrderModel->latest_track_time = $deliveryStepList[1]['operationTime'];
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
//                        echo DeliveryOrder::STATUS_TRANSPORTING . "——" . $deliveryStepList[1]['operationDescribe'] . $deliveryStepList[1]['operationTime'] . "\r\n";
                        if (!$dryRun) {
                            if (!$deliveryOrderModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                        }
                    } else {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流信息失败，原因：物流信息格式错误" . Utility::arrayToString($deliveryStepList));
                    }
                }
                if (count($deliveryStepList) > 2) {
                    unset($deliveryStepList[0]);
                    unset($deliveryStepList[1]);
                }
                foreach ($deliveryStepList as $deliveryStep) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $operationTime = '';
                        $operationDescribe = '';
                        $operationDescribe = $deliveryStep['operationDescribe'];
                        $operationTime = $deliveryStep['operationTime'];
                        $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                        if ((strpos($operationDescribe, '到达') !== false && ((strpos($operationDescribe, '邮政所') !== false) || (strpos($operationDescribe, '揽投部') !== false)) && strpos($operationDescribe, '代签收') === false) || strpos($operationDescribe, '快件已到达') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_TRANSPORTED) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_TRANSPORTED;
                            }
                            if (empty($deliveryOrderModel->transported_time)) {
                                $deliveryOrderModel->transported_time = $operationTime;
                            }

                        } elseif (strpos($operationDescribe, '配送中') !== false || strpos($operationDescribe, '派送中') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_DELIVERING) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_DELIVERING;
                            }
                            if (empty($deliveryOrderModel->delivering_time)) {
                                $deliveryOrderModel->delivering_time = $operationTime;
                            }

                        } elseif (((strpos($operationDescribe, '代签收') !== false || strpos($operationDescribe, '自提') !== false || strpos($operationDescribe, '驿站') !== false || strpos($operationDescribe, '村邮站') !== false) && strpos($operationDescribe, '退回') === false) || (strpos($operationDescribe, '签收') !== false && strpos($operationDescribe, '本人') === false && strpos($operationDescribe, '准备') === false)) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_REPLACE_DELIVERED) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_REPLACE_DELIVERED;
                                $deliveryOrderModel->replace_delivered_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            }
                        } elseif (strpos($operationDescribe, '本人签收') !== false || strpos($operationDescribe, '【本人】签收') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_DELIVERED) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_DELIVERED;
                                $deliveryOrderModel->delivered_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            }
                        } elseif (strpos($operationDescribe, '退回') !== false || strpos($operationDescribe, '退件') !== false || strpos($operationDescribe, '寄件人签收') !== false || strpos($operationDescribe, '成功退回至寄件人') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_REJECT) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_REJECT;
                                $deliveryOrderModel->reject_time = $operationTime;
                            }
                        }
                        $deliveryOrderModel->latest_track_info = $operationDescribe;
                        $deliveryOrderModel->latest_track_time = $operationTime;
                        $deliveryOrderModel->update_name = 'system';
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                        if (!$dryRun) {
                            if (!$deliveryOrderModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                        }

//                        echo $deliveryOrderModel->status . "——" . $operationDescribe . "——" . $operationTime . "\r\n";
                        $deliveryInfoExist = DeliveryInfo::find()->where(['logistic_no' => $logisticNo, 'content' => $operationDescribe, 'update_time' => $operationTime])->exists();
                        if (!$deliveryInfoExist) {
                            $deliveryInfoModel = new DeliveryInfo();
                            $deliveryInfoModel->logistic_no = $logisticNo;
                            $deliveryInfoModel->status = $deliveryOrderModel->status;
                            $deliveryInfoModel->content = $operationDescribe;
                            $deliveryInfoModel->update_time = $operationTime;
                            if (!$dryRun) {
                                if (!$deliveryInfoModel->save()) {
                                    throw new Exception("快递单号：" . $logisticNo . "新增物流信息失败，原因：" . Utility::arrayToString($deliveryInfoModel->getErrors()));
                                }
                            }
                        }
                        $transaction->commit();
                    } catch (Exception $exception) {
                        $transaction->rollBack();
                        echo "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $exception->getMessage() . "\r\n";
                    }
                }
                echo "快递单号：" . $logisticNo . "更新运单信息成功！\r\n";
            } catch
            (Exception $e) {
                echo "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $e->getMessage() . "\r\n";
            }
        }
    }

    /**
     *
     * ./yii delivery-info/fix 0 '2023-10-08 09:00:00' '2023-10-08 10:00:00' '' 1 1
     * @param int $dryRun
     * @param string $logisticNo
     * @param string $startTime
     * @param string $endTime
     * @param int $type
     * @param int $isFix
     * @throws Exception
     */
    public function actionFix($dryRun = 1, $startTime = '', $endTime = '', $logisticNo = '', $type = 1, $isFix = 0)
    {
        $fixSql = "SELECT * FROM `delivery_order` WHERE 1 ";
//        $fixSql = "SELECT * FROM `delivery_order` WHERE  latest_track_info = '收件人申请改址成功，将按照更改后地址进行派送。'";

        if (!empty($startTime) && !empty($endTime)) {
            $fixSql .= " AND create_time >= '" . $startTime . "' and create_time <= '" . $endTime . "' ";
        }
        if (!empty($logisticNo)) {
            $fixSql .= ' AND logistic_no = "' . $logisticNo . '" ';
        }
        $fixSql .= ' order by create_time ASC ';
        echo "fixSql:" . $fixSql . "\r\n";
        $result = \Yii::$app->db->createCommand($fixSql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                $deliverySteps = [];
                $deliveryStepList = [];
                $logisticNo = $deliveryOrder['logistic_no'];
                $deliveryInfoRes = EmsCloud::getDeliveryInfo($logisticNo); //获取物流轨迹
//                    ActionLog::log($logisticNo, 'delivery-info/fix', $deliveryInfoRes, 'system');
                $deliveryInfo = json_decode($deliveryInfoRes, true);
//                    print_r($deliveryInfo);
                if (empty($deliveryInfo['traces'])) {
                    throw new Exception("快递单号：" . $logisticNo . "获取物流轨迹失败，原因：轨迹信息为空！");
                }
                foreach ($deliveryInfo['traces'] as $key => $value) {
                    $deliverySteps[$key]['operationDescribe'] = $value['remark'];
                    $deliverySteps[$key]['operationTime'] = $value['acceptTime'];
                }
//                    print_r($deliverySteps);
//                    ActionLog::log($logisticNo . '-steps', 'delivery-info/fix', $deliverySteps, 'system');

                if (empty($deliverySteps)) {
                    throw new Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：轨迹信息为空！");
                }
                $deliveryStepList = Utility::order_date_array($deliverySteps, 'asc', 'operationTime');
//                print_r($deliveryStepList);
                if ($isFix) {
                    DeliveryOrder::updateAll(['status' => DeliveryOrder::STATUS_SYNC, 'send_time' => '', 'latest_track_info' => '', 'latest_track_time' => '', 'transporting_time' => '', 'transported_time' => '', 'delivering_time' => '', 'replace_delivered_time' => '', 'delivered_time' => '', 'finish_time' => '', 'reject_time' => '', 'update_time' => ''], ['logistic_no' => $logisticNo]);
                    DeliveryInfo::deleteAll(['logistic_no' => $logisticNo]);
                }
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                if ($deliveryOrderModel->status == DeliveryOrder::STATUS_SYNC) {
                    if (isset($deliveryStepList[0]['operationDescribe']) && isset($deliveryStepList[0]['operationTime'])) {
                        $deliveryOrderModel->status = DeliveryOrder::STATUS_SEND;
                        $deliveryOrderModel->send_time = $deliveryStepList[0]['operationTime'];
                        $deliveryOrderModel->latest_track_info = $deliveryStepList[0]['operationDescribe'];
                        $deliveryOrderModel->latest_track_time = $deliveryStepList[0]['operationTime'];
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                        echo DeliveryOrder::STATUS_SEND . "——" . $deliveryStepList[0]['operationDescribe'] . $deliveryStepList[0]['operationTime'] . "\r\n";
                        if (!$dryRun) {
                            if (!$deliveryOrderModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                        }
                    } else {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流信息失败，原因：物流信息格式错误" . Utility::arrayToString($deliverySteps));
                    }
                }
                if ($deliveryOrderModel->status == DeliveryOrder::STATUS_SEND) {
                    if (isset($deliveryStepList[1]['operationDescribe']) && isset($deliveryStepList[1]['operationTime'])) {
                        $deliveryOrderModel->status = DeliveryOrder::STATUS_TRANSPORTING;
                        $deliveryOrderModel->transporting_time = $deliveryStepList[1]['operationTime'];
                        $deliveryOrderModel->latest_track_info = $deliveryStepList[1]['operationDescribe'];
                        $deliveryOrderModel->latest_track_time = $deliveryStepList[1]['operationTime'];
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                        echo DeliveryOrder::STATUS_SEND . "——" . $deliveryStepList[1]['operationDescribe'] . $deliveryStepList[1]['operationTime'] . "\r\n";
                        if (!$dryRun) {
                            if (!$deliveryOrderModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                        }
                    } else {
                        throw new Exception("快递单号：" . $logisticNo . "获取物流信息失败，原因：物流信息格式错误" . Utility::arrayToString($deliveryStepList));
                    }
                }
                if (count($deliveryStepList) > 2) {
                    unset($deliveryStepList[0]);
                    unset($deliveryStepList[1]);
                }
                foreach ($deliveryStepList as $deliveryStep) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $operationDescribe = $deliveryStep['operationDescribe'];
                        $operationTime = $deliveryStep['operationTime'];
                        $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                        if (strpos($operationDescribe, '到达') !== false && ((strpos($operationDescribe, '邮政所') !== false) || (strpos($operationDescribe, '揽投部') !== false)) && strpos($operationDescribe, '代签收') === false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_TRANSPORTED) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_TRANSPORTED;
                            }
                            if (empty($deliveryOrderModel->transported_time)) {
                                $deliveryOrderModel->transported_time = $operationTime;
                            }

                        } elseif (strpos($operationDescribe, '配送中') !== false || strpos($operationDescribe, '派送中') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_DELIVERING) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_DELIVERING;
                            }
                            if (empty($deliveryOrderModel->delivering_time)) {
                                $deliveryOrderModel->delivering_time = $operationTime;
                            }

                        } elseif (((strpos($operationDescribe, '代签收') !== false || strpos($operationDescribe, '自提') !== false || strpos($operationDescribe, '驿站') !== false || strpos($operationDescribe, '村邮站') !== false) && strpos($operationDescribe, '退回') === false) || (strpos($operationDescribe, '签收') !== false && strpos($operationDescribe, '本人') === false && strpos($operationDescribe, '准备') === false)) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_REPLACE_DELIVERED) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_REPLACE_DELIVERED;
                                $deliveryOrderModel->replace_delivered_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            }
                        } elseif (strpos($operationDescribe, '本人签收') !== false || strpos($operationDescribe, '【本人】签收') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_DELIVERED) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_DELIVERED;
                                $deliveryOrderModel->delivered_time = $operationTime;
                                $deliveryOrderModel->finish_time = $operationTime;
                            }
                            } elseif (strpos($operationDescribe, '退回') !== false || strpos($operationDescribe, '退件') !== false || strpos($operationDescribe, '寄件人签收') !== false || strpos($operationDescribe, '成功退回至寄件人') !== false) {
                            if ($deliveryOrderModel->status < DeliveryOrder::STATUS_REJECT) {
                                $deliveryOrderModel->status = DeliveryOrder::STATUS_REJECT;
                                $deliveryOrderModel->reject_time = $operationTime;
                            }
                        }
                        $deliveryOrderModel->latest_track_info = $operationDescribe;
                        $deliveryOrderModel->latest_track_time = $operationTime;
                        $deliveryOrderModel->update_name = 'system';
                        $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                        if (!$dryRun) {
                            if (!$deliveryOrderModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                            }
                        }

                        echo $deliveryOrderModel->status . "——" . $operationDescribe . "——" . $operationTime . "\r\n";
                        $deliveryInfoExist = DeliveryInfo::find()->where(['logistic_no' => $logisticNo, 'content' => $operationDescribe, 'update_time' => $operationTime])->exists();
                        if (!$deliveryInfoExist) {
                            $deliveryInfoModel = new DeliveryInfo();
                            $deliveryInfoModel->logistic_no = $logisticNo;
                            $deliveryInfoModel->status = $deliveryOrderModel->status;
                            $deliveryInfoModel->content = $operationDescribe;
                            $deliveryInfoModel->update_time = $operationTime;
                            if (!$dryRun) {
                                if (!$deliveryInfoModel->save()) {
                                    throw new Exception("快递单号：" . $logisticNo . "新增物流信息失败，原因：" . Utility::arrayToString($deliveryInfoModel->getErrors()));
                                }
                            }
                        }
                        $transaction->commit();
                    } catch (Exception $exception) {
                        $transaction->rollBack();
                        echo "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $exception->getMessage() . "\r\n";
                    }
                }
                echo "快递单号：" . $logisticNo . "更新运单信息成功！\r\n";
            } catch
            (Exception $e) {
                echo "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $e->getMessage() . "\r\n";
            }
        }
    }

    /**
     *
     * ./yii delivery-info/fix-sec 1 '' '' '' 3 235
     * @param int $dryRun
     * @param string $institutionId
     * @param string $customerId
     * @param string $logisticNo
     * @param string $startTime
     * @param string $endTime
     * @param string $status
     * @throws Exception
     */
    public
    function actionFixSec($dryRun = 1, $startTime = '', $endTime = '', $status = '', $institutionId = '', $customerId = '', $logisticNo = '')
    {
        $sql = 'SELECT d.logistic_no,d.sec_logistic_id, d.sec_logistic_no, d.status,i.content FROM `delivery_order` d left join delivery_info i on d.logistic_no = i.logistic_no  where d.sec_logistic_id is null and i.content like "%转发%"';

        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }

        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            $logisticNo = $deliveryOrder['logistic_no'];
            if (!empty($deliveryOrder['content'])) {
                $pattern = '/\[(.*?)\]/';
                preg_match_all($pattern, $deliveryOrder['content'], $matches);
                if (!empty($matches[1])) {
                    $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);
                    if (isset($matches[1][0]) && !empty($matches[1][0])) {
                        $secLogisticCompany = $matches[1][0];
                        $logisticCompanyModel = LogisticCompany::findOne(['company_name' => $secLogisticCompany]);
                        if (!$logisticCompanyModel) {
                            $logisticCompanyModel = new LogisticCompany();
                            $logisticCompanyModel->company_name = $secLogisticCompany;
                            if (!$logisticCompanyModel->save()) {
                                throw new Exception("快递单号：" . $logisticNo . "新增快递公司失败，原因：" . Utility::arrayToString($logisticCompanyModel->getErrors()));
                            }
                            $secLogisticId = $logisticCompanyModel->attributes['id'];
                        } else {
                            $secLogisticId = $logisticCompanyModel->id;
                        }

                        $deliveryOrderModel->sec_logistic_id = $secLogisticId;

                    }
                    if (isset($matches[1][1]) && !empty($matches[1][1])) {
                        $secLogisticNo = $matches[1][1];
                        $deliveryOrderModel->sec_logistic_no = $secLogisticNo;
                    }
                    if (!$dryRun) {
                        if (!$deliveryOrderModel->save()) {
                            throw new Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                        }
                    }
                    echo "快递单号：" . $logisticNo . ',sec_logistic_id:' . $secLogisticId . ',sec_logistic_no:' . $secLogisticNo . "\r\n";
                }
            }

        }
    }


    /**
     * ./yii delivery-info/fix-delivering-and-sec
     *
     * @param string $logisticNo
     * @throws Exception
     */
    public
    function actionFixDeliveringAndSec($logisticNo = '')
    {
        $sql = 'SELECT logistic_no,order_no from delivery_order where status = ' . DeliveryOrder::STATUS_TRANSPORTED . ' and TIMESTAMPDIFF(HOUR, transported_time, NOW()) >= 5 ';
        if (!empty($logisticNo)) {
            $sql .= " and logistic_no = '" . $logisticNo . "' ";
        }
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        $status = DeliveryOrder::STATUS_DELIVERING;

        foreach ($result as $deliveryOrder) {
            try {
                $logisticNo = $deliveryOrder['logistic_no'];
                $orderNo = $deliveryOrder['order_no'];
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo]);

                $deliveryInfoRes = DeliveryInfo::getContentAndTimeByLogisticNoAndStatus($logisticNo, $orderNo, $status);
                if (!$deliveryInfoRes['success']) {
                    throw new Exception("快递单号：" . $logisticNo . "获取状态：" . $status . "的物流轨迹失败，原因：" . $deliveryInfoRes['msg'] . "。");
                }
                if (!empty($deliveryInfoRes['res']['time']) && $deliveryInfoRes['res']['secLogisticCompany'] && $deliveryInfoRes['res']['secLogisticNo']) {

                    $time = $deliveryInfoRes['res']['time'];
                    $content = $deliveryInfoRes['res']['content'];
                    $secLogisticCompany = $deliveryInfoRes['res']['secLogisticCompany'];
                    $secLogisticNo = $deliveryInfoRes['res']['secLogisticNo'];
                    $deliveryOrderModel->status = $status;
                    $deliveryOrderModel->delivering_time = $time;
                    $secLogisticCompany = $secLogisticCompany;
                    $secLogisticNo = $secLogisticNo;
                    $logisticCompanyModel = LogisticCompany::findOne(['company_name' => $secLogisticCompany]);
                    if (!$logisticCompanyModel) {
                        $logisticCompanyModel = new LogisticCompany();
                        $logisticCompanyModel->company_name = $secLogisticCompany;
                        if (!$logisticCompanyModel->save()) {
                            throw new Exception("快递单号：" . $logisticNo . "新增快递公司失败，原因：" . Utility::arrayToString($logisticCompanyModel->getErrors()));
                        }
                        $secLogisticId = $logisticCompanyModel->attributes['id'];
                    } else {
                        $secLogisticId = $logisticCompanyModel->id;
                    }
                    $deliveryOrderModel->sec_logistic_id = $secLogisticId;
                    $deliveryOrderModel->sec_logistic_no = $secLogisticNo;
                    $deliveryOrderModel->latest_track_info = $content;
                    if (!$deliveryOrderModel->save()) {
                        throw  new \Exception("快递单号：" . $logisticNo . "更新运单信息失败，原因：" . Utility::arrayToString($deliveryOrderModel->getErrors()));
                    }
                    echo "logistic_no:" . $logisticNo . ",delivering_time:" . $time . ",sec_logistic_id:" . $secLogisticId . ",sec_logistic_no:" . $secLogisticNo . ",latest_track_info:" . $content . "\r\n";
                    $deliveryInfoExist = DeliveryInfo::find()->where(['logistic_no' => $logisticNo, 'status' => DeliveryOrder::STATUS_DELIVERING, 'content' => $content, 'update_time' => $time])->exists();
                    if (!$deliveryInfoExist) {
                        $deliveryInfoModel = new DeliveryInfo();
                        $deliveryInfoModel->logistic_no = $logisticNo;
                        $deliveryInfoModel->status = $status;
                        $deliveryInfoModel->content = $content;
                        $deliveryInfoModel->update_time = $time;
                        if (!$deliveryInfoModel->save()) {
                            throw  new \Exception("快递单号：" . $logisticNo . "新增物流信息失败，原因：" . Utility::arrayToString($deliveryInfoModel->getErrors()));
                        }
                    }
                    $zjsDeliveryInfoExist = ZjsDeliveryInfo::find()->where(['mail_no' => $logisticNo, '`desc`' => $content, 'time' => $time])->exists();
                    if (!$zjsDeliveryInfoExist) {
                        $deliveryInfoModel = new ZjsDeliveryInfo();
                        $deliveryInfoModel->mail_no = $logisticNo;
                        $deliveryInfoModel->desc = $content;
                        $deliveryInfoModel->time = $time;
                        $deliveryInfoModel->order_no = $deliveryOrderModel['order_no'];
                        if (!$deliveryInfoModel->save()) {
                            throw  new \Exception("快递单号：" . $logisticNo . "新增宅急送物流信息失败，原因：" . Utility::arrayToString($deliveryInfoModel->getErrors()));
                        }
                    }
                } else {
                    echo "没有转单记录！" . "\r\n";
                }
                sleep(1);
            } catch (Exception $e) {
                echo "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $e->getMessage() . "\r\n";
            }
        }
    }

    /**
     * ./yii delivery-info/subscribe-zjs-push
     *
     * @param string $startTime
     * @param string $endTime
     * @param string $logisticNo
     * @throws Exception
     */
    public
    function actionSubscribeZjsPush($startTime = '', $endTime = '', $logisticNo = '')
    {
        date_default_timezone_set("Asia/Shanghai");

        $sql = 'SELECT logistic_no, is_subscribe_zjs_push FROM `delivery_order`  where is_subscribe_zjs_push = 0 ';
        if (!empty($logisticNo)) {
            $sql .= ' AND logistic_no = "' . $logisticNo . '" ';
        } else {
            if (empty($startTime)) {
                $startTime = date('Y-m-d H:i:s', strtotime('-40 minute'));
            }
            if (empty($endTime)) {
                $endTime = date('Y-m-d H:i:s', time());
            }
            $sql .= " and create_time >= '" . $startTime . "' and  create_time <= '" . $endTime . "' ";
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
                $logisticNo = $deliveryOrder['logistic_no'];
                $subscribeRes = ZjsCloud::getSubscribe($logisticNo);
                if (!$subscribeRes['success']) {
                    throw new \Exception("运单号：" . $logisticNo . "推送宅急送订阅接口失败，原因：" . $subscribeRes['msg']);
                } else {
                    if (isset($subscribeRes['data'])) {
                        $zjsRes = json_decode($subscribeRes['data'], true);
                        if ($zjsRes['code'] != '10' && $zjsRes['description'] != '成功') {
                            throw new \Exception("运单号：" . $logisticNo . "推送宅急送订阅接口失败，原因：" . $zjsRes['description']);
                        }
                    }
                }

                DeliveryOrder::updateAll(['is_subscribe_zjs_push' => 1], ['logistic_no' => $logisticNo]);
                echo "运单号：" . $logisticNo . "推送宅急送订阅接口成功！" . "\r\n";
            } catch (\Exception $exception) {
                echo $exception->getMessage() . "\r\n";
            }
        }

    }

    /**
     * ./yii delivery-info/get-delivery-info
     *
     * @param string $orderNo
     */
    public
    function actionGetDeliveryInfo($orderNo = '')
    {
        $get_content = '';
        $url = 'http://211.156.193.140:8000/cotrackapi/api/track/mail/9717556397049';
        $header[] = "version:ems_track_cn_1.0";
        $header[] = "authenticate:shandongems_zd3fcq8jv2cvw4hsk";
        $curl_option = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $header
        );
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            if (is_array($curl_option) && !empty($curl_option)) {
                foreach ($curl_option as $key => $val) {
                    curl_setopt($curl, $key, $val);
                }
            }
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);

            $get_content = curl_exec($curl);
            if (curl_error($curl)) {
                echo curl_error($curl);
            }
            curl_close($curl);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        echo $get_content;
        exit;
        $orderNo = '78365275168226';
        $key = 'C47527C3F2DD263FF46FE86FAAB35294';
        $url = 'http://japi.zto.cn/zto/api_utf8/traceInterface';
        $company_id = '9010f8a380d44f77b2032808bc8bd445';
        $request_data = json_encode(array($orderNo));
        $data_digest = MD5($request_data . $key);
        $url = $url . "?data=" . $request_data . "&data_digest=" . $data_digest . "&msg_type=TRACES&company_id=" . $company_id;
        $logistic_track_info_source = $this->get_content($url);
        print_r($logistic_track_info_source);
        exit;

    }

    public
    function get_content($url)
    {
        try {
            $opts = array(
                'http' => array(
                    'method' => "GET",
                    'timeout' => 10, //设置超时
                )
            );
            $context = stream_context_create($opts);
            $get_content = file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $get_content;
    }

}
<?php

namespace console\controllers;

use backend\models\Institution;
use common\components\Utility;
use common\models\CustomerSettlementOrder;
use common\models\CustomerSettlementOrderDetail;
use common\models\DeliveryOrder;
use yii\console\Controller;

class CustomerSettlementOrderController extends Controller
{
    /**
     * ./yii customer-settlement-order/run 1 '' '' 4 112
     *
     * @param $logisticNo
     * @param $paramInstitutionId
     * @param $customerId
     * @param $startTime
     * @param $endTime
     * @param $dryRun
     */
    public function actionRun($dryRun = 1, $startTime = '', $endTime = '', $paramInstitutionId = '', $customerId = '')
    {
        date_default_timezone_set("Asia/Shanghai");
        if (!empty($institutionId)) {
            $sql = "SELECT * FROM " . Institution::tableName() . " WHERE status = '" . Institution::STATUS_NORMAL . "' AND id = '" . $paramInstitutionId . "' order by id DESC";
        } else {
            $sql = "SELECT * FROM " . Institution::tableName() . " WHERE status = '" . Institution::STATUS_NORMAL . "' order by id DESC";

        }
        echo "sql:" . $sql . "\r\n";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        if (empty($result)) {
            echo "没有需要处理的组织机构。";
            exit;
        }
        echo "有:" . count($result) . "个组织机构需要处理\r\n";

        foreach ($result as $item) {
            try {
                $institutionId = $item['id'];
                if (empty($startTime)) {
                    $startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
                }
                if (empty($endTime)) {
                    $endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));
                }
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $customerSettlementOrderDetailSql = "SELECT * FROM " . CustomerSettlementOrderDetail::tableName() . " WHERE finish_time >= '" . $startTime . "' AND  finish_time <= '" . $endTime . "' AND institution_id = " . $item['id'] . " ";
                    if (!empty($customerId)) {
                        $customerSettlementOrderDetailSql .= " and  customer_id = '" . $customerId . "' ";
                    }
                    $customerSettlementOrderDetailSql .= " ORDER BY create_time DESC";
echo "customerSettlementOrderDetailSql:" . $customerSettlementOrderDetailSql . "\r\n";
                    $customerSettlementOrderDetailRes = \Yii::$app->db->createCommand($customerSettlementOrderDetailSql)->queryAll();
                    if (empty($customerSettlementOrderDetailRes)) {
                        throw new \Exception("组织机构ID：" . $institutionId . "没有需要处理的结算单明细。");
                    }
                    $customerSettlementOrderList = [];
                    $detailCustomerIdMap = [];
                    $logisticNoIsSettlementMap = [];
                    foreach ($customerSettlementOrderDetailRes as $item) {
                        if (!isset($customerSettlementOrderList[$item['customer_id']])) {
                            $customerSettlementOrderList[$item['customer_id']]['need_receipt_amount'] = 0;
                        }
                        $customerSettlementOrderList[$item['customer_id']]['need_receipt_amount'] += $item['need_receipt_amount'];
                    }
                    foreach ($customerSettlementOrderDetailRes as $i) {
                        $detailCustomerIdMap[$i['customer_id']][] = $i['id'];
                        $logisticNoIsSettlementMap[$i['customer_id']][] = $i['logistic_no'];
                    }
                    if (!empty($customerSettlementOrderList)) {
                        foreach ($customerSettlementOrderList as $customerIdKey => $details) {
                            $needReceiptAmount = $details['need_receipt_amount'];
                            $customerSettlementOrderModel = CustomerSettlementOrder::findOne(['institution_id' => $institutionId, 'customer_id' => $customerId, 'start_time' => $startTime, 'end_time' => $endTime]);
                            if (!$customerSettlementOrderModel) {
                                $customerSettlementOrderModel = new CustomerSettlementOrder();
                                $settlementOrderNo = CustomerSettlementOrder::generateId();
                                $customerSettlementOrderModel->settlement_order_no = $settlementOrderNo;
                                $customerSettlementOrderModel->institution_id = $institutionId;
                                $customerSettlementOrderModel->customer_id = $customerIdKey;
                                $customerSettlementOrderModel->need_receipt_amount = $needReceiptAmount;
                                $customerSettlementOrderModel->need_amount = $needReceiptAmount;
                                $customerSettlementOrderModel->start_time = $startTime;
                                $customerSettlementOrderModel->end_time = $endTime;
                                $customerSettlementOrderModel->status = CustomerSettlementOrder::STATUS_WAIT;
                                $customerSettlementOrderModel->create_time = date('Y-m-d H:i:s', time());
                                $customerSettlementOrderModel->create_name = 'system';
                            } else {
                                $settlementOrderNo = $customerSettlementOrderModel->settlement_order_no;

                                if ($customerSettlementOrderModel->need_receipt_amount != $needReceiptAmount) {
                                    $customerSettlementOrderModel->need_receipt_amount = $needReceiptAmount;
                                    $customerSettlementOrderModel->need_amount = $needReceiptAmount;
                                }
                            }

                            if (!$dryRun) {
                                if (!$customerSettlementOrderModel->save()) {
                                    throw new \Exception(Utility::arrayToString($customerSettlementOrderModel->getErrors()));
                                }
                            }
                            echo "组织机构ID：" . $institutionId . ", 客户ID:" . $customerIdKey . ",应收金额:" . $needReceiptAmount . ",应结算金额:" . $needReceiptAmount . ",结算单号:" . $settlementOrderNo . "\r\n";
                            echo "明细列表：" . Utility::arrayToString($detailCustomerIdMap) . "\r\n";
//                            echo "运单列表：" . Utility::arrayToString($logisticNoIsSettlementMap) . "\r\n";
                            if (!empty($detailCustomerIdMap)) {
                                if (!$dryRun) {
                                    CustomerSettlementOrderDetail::updateAll(['settlement_order_no' => $settlementOrderNo], ['in', 'id', $detailCustomerIdMap[$customerIdKey]]);
                                }
                            }
//                            if (!empty($logisticNoIsSettlementMap)) {
//                                if (!$dryRun) {
//                                    DeliveryOrder::updateAll(['is_customer_settle' => DeliveryOrder::YES], ['in', 'logistic_no', $logisticNoIsSettlementMap[$customerIdKey]]);
//                                }
//                            }
                            if (!$dryRun) {
                                $transaction->commit();
                            }

                        }
                    }

                } catch (\Exception $e) {
                    if (!$dryRun) {
                        $transaction->rollBack();
                    }
                    echo $e->getMessage() . "\r\n";
                }

//                $detailBatchAddRes = CustomerSettlementOrderDetail::batchAdd($item['id'], $customerId, $startTime, $endTime);
//                if (!$detailBatchAddRes['success']) {
//                    throw new \Exception(Utility::arrayToString($detailBatchAddRes['errorList']));
//                }
//                $orderAddRes = CustomerSettlementOrder::add($item['id'], $customerId, $startTime, $endTime);
//                if (!$orderAddRes['success']) {
//                    throw new \Exception(Utility::arrayToString($orderAddRes['errorList']));
//                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }

        }
        echo "finished";
    }
}
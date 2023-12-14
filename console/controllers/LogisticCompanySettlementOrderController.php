<?php

namespace console\controllers;

use backend\models\CustomerAreaDeliveryFee;
use backend\models\Institution;
use common\components\Utility;
use common\models\Cnarea;
use common\models\Customer;
use common\models\CustomerSettlementOrderDetail;
use common\models\DeliveryOrder;
use common\models\LogisticCompany;
use common\models\LogisticCompanyFeeRules;
use common\models\LogisticCompanySettlementOrder;
use common\models\LogisticCompanySettlementOrderDetail;
use yii\console\Controller;

class LogisticCompanySettlementOrderController extends Controller
{
    /**
     * ./yii logistic-company-settlement-order/run '' '' '16'
     *
     * @param $logisticId
     * @param $startTime
     * @param $endTime
     */
    public function actionRun($startTime = '', $endTime = '', $logisticId = '')
    {
        $sql = "SELECT * FROM " . LogisticCompany::tableName() . " WHERE status = " . LogisticCompany::STATUS_ENABLE;
        if (!empty($logisticId)) {
            $sql .= " AND id =  " . $logisticId . "  ";
        }
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        if (empty($result)) {
            echo "没有需要处理的快递公司。";
            exit;
        }
        echo "有:" . count($result) . "个快递公司需要处理\r\n";

        foreach ($result as $item) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $logisticId = $item['id'];
                if (empty($startTime)) {
                    $startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
                }
                if (empty($endTime)) {
                    $endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));
                }
                $logisticCompanySettlementOrderDetailSql = "SELECT * FROM " . LogisticCompanySettlementOrderDetail::tableName() . " WHERE create_time >= '" . $startTime . "' AND  create_time <= '" . $endTime . "' AND logistic_id = " . $item['id'] . " ORDER BY create_time DESC";
                echo "logisticCompanySettlementOrderDetailSql:" . $logisticCompanySettlementOrderDetailSql . "\r\n";
                $logisticCompanySettlementOrderDetailRes = \Yii::$app->db->createCommand($logisticCompanySettlementOrderDetailSql)->queryAll();
                if (empty($logisticCompanySettlementOrderDetailRes)) {
                    throw new \Exception("快递公司ID：" . $logisticId . "没有需要处理的结算单明细。");
                }
                $logisticCompanySettlementOrderList = [];
                $detailLogisticIdMap = [];
                $logisticNoIsSettlementMap = [];
                foreach ($logisticCompanySettlementOrderDetailRes as $item) {
                    if (!isset($logisticCompanySettlementOrderList[$item['logistic_id']][$item['warehouse_code']])) {
                        $logisticCompanySettlementOrderList[$item['logistic_id']][$item['warehouse_code']]['need_receipt_amount'] = 0;
                    }
                    $logisticCompanySettlementOrderList[$item['logistic_id']][$item['warehouse_code']]['need_receipt_amount'] += $item['need_receipt_amount'];
                }
                foreach ($logisticCompanySettlementOrderDetailRes as $i) {
                    $detailLogisticIdMap[$i['logistic_id']][] = $i['id'];
                    $logisticNoIsSettlementMap[$i['logistic_id']][] = $i['logistic_no'];
                }
                if (!empty($logisticCompanySettlementOrderList)) {
                    foreach ($logisticCompanySettlementOrderList as $logisticIdKey => $details) {
                        foreach ($details as $warehouseCode => $value) {
                            $needReceiptAmount = $details['need_receipt_amount'];
                            $logisticCompanySettlementOrderModel = LogisticCompanySettlementOrder::findOne(['logistic_id' => $logisticId, 'start_time' => $startTime, 'end_time' => $endTime]);
                            if (!$logisticCompanySettlementOrderModel) {
                                $logisticCompanySettlementOrderModel = new LogisticCompanySettlementOrder();
                                $settlementOrderNo = LogisticCompanySettlementOrder::generateId();
                                $logisticCompanySettlementOrderModel->settlement_order_no = $settlementOrderNo;
                                $logisticCompanySettlementOrderModel->logistic_id = $logisticId;
                                $logisticCompanySettlementOrderModel->warehouse_code = $warehouseCode;
                                $logisticCompanySettlementOrderModel->type = LogisticCompanySettlementOrder::TYPE_PAY;
                                $logisticCompanySettlementOrderModel->date = date('Y-m-d', time());
                                $logisticCompanySettlementOrderModel->order_num = count($details);
                                $logisticCompanySettlementOrderModel->need_receipt_amount = $needReceiptAmount;
                                $logisticCompanySettlementOrderModel->need_amount = $needReceiptAmount;
                                $logisticCompanySettlementOrderModel->start_time = $startTime;
                                $logisticCompanySettlementOrderModel->end_time = $endTime;
                                $logisticCompanySettlementOrderModel->status = LogisticCompanySettlementOrder::STATUS_WAIT;
                                $logisticCompanySettlementOrderModel->create_time = date('Y-m-d H:i:s', time());
                                $logisticCompanySettlementOrderModel->create_name = 'system';
                            } else {
                                $settlementOrderNo = $logisticCompanySettlementOrderModel->settlement_order_no;

                                if ($logisticCompanySettlementOrderModel->need_receipt_amount != $needReceiptAmount) {
                                    $logisticCompanySettlementOrderModel->need_receipt_amount = $needReceiptAmount;
                                    $logisticCompanySettlementOrderModel->need_amount = $needReceiptAmount;
                                }
                            }

                            if (!$logisticCompanySettlementOrderModel->save()) {
                                throw new \Exception(Utility::arrayToString($logisticCompanySettlementOrderModel->getErrors()));
                            }
                            if (!empty($detailLogisticIdMap)) {
                                CustomerSettlementOrderDetail::updateAll(['settlement_order_no' => $settlementOrderNo], ['in', 'id', $detailLogisticIdMap[$logisticIdKey]]);
                                DeliveryOrder::updateAll(['is_logistic_company_settle' => DeliveryOrder::YES], ['in', 'id', $detailLogisticIdMap[$logisticIdKey]]);

                            }
                            $transaction->commit();
                            echo "success\r\n";
                        }
                    }
                }

            } catch (\Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage() . "\r\n";
            }

        }
        echo "finished";
    }
}

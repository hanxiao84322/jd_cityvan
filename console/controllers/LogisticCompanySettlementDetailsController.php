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
                foreach ($deliveryOrderResult as $deliveryOrder) {
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
                        echo "success\r\n";
                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\r\n";
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
    }

    /**
     * ./yii logistic-company-settlement-details/jd-run '' '' '' ''
     *
     * @param $logisticNo
     * @param $warehouseCode
     * @param $startTime
     * @param $endTime
     */
    public function actionJdRun($startTime = '', $endTime = '', $warehouseCode = '', $logisticNo = '')
    {
        $sql = "SELECT DISTINCT warehouse_code FROM " . LogisticCompanyFeeRules::tableName() . " WHERE  type = " . LogisticCompanyFeeRules::TYPE_WAREHOUSE . "  ";
        if (!empty($warehouseCode)) {
            $sql .= " AND warehouse_code =  " . $warehouseCode . "  ";
        }
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        if (empty($result)) {
            echo "没有需要处理的仓库编码。";
            exit;
        }
        echo "有:" . count($result) . "个仓库需要处理\r\n";

        foreach ($result as $item) {
            try {
                if (empty($startTime)) {
                    $startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
                }
                if (empty($endTime)) {
                    $endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));
                }
                $deliveryOrderSql = "SELECT * FROM delivery_order where create_time > '2023-10-01' AND create_time >= '" . $startTime . "' AND create_time <= '" . $endTime . "' and warehouse_code = '" . $item['warehouse_code'] . "' AND is_logistic_company_settle = 0 ";
                if (!empty($logisticNo)) {
                    $deliveryOrderSql .= " AND logistic_no = '" . $logisticNo . "' ";
                }

                echo "deliveryOrderSql:" . $deliveryOrderSql . "\r\n";
                $deliveryOrderResult = \Yii::$app->db->createCommand($deliveryOrderSql)->queryAll();
                if (empty($deliveryOrderResult)) {
                    throw new \Exception("仓库编码：" . $item['warehouse_code'] . "没有需要处理的订单。");
                }
                echo "仓库编码：" . $warehouseCode . "有:" . count($deliveryOrderResult) . "条运单需要处理\r\n";
                foreach ($deliveryOrderResult as $deliveryOrder) {
                    try {
                        $itemLogisticNo = $deliveryOrder['logistic_no'];
                        $itemLogisticId = $deliveryOrder['logistic_id'];
                        $itemWarehouseCode = $deliveryOrder['warehouse_code'];
                        $jdWeight = $deliveryOrder['shipping_weight_rep']; //京东结算取包裹重量
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
                                        throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "没有对应的运费规则");

                                    }
                                }
                            }
                        }
                        switch ($logisticCompanyFeeRulesResult['weight_round_rule']) {
                            case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_NOT_UP:
                                $jdWeight = intval($jdWeight);
                                break;
                            case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_HALF_UP:
                                $jdWeight = round($jdWeight);
                                break;
                            case LogisticCompanyFeeRules::WEIGHT_ROUND_RULE_UP:
                                $jdWeight = ceil($jdWeight);
                                break;
                            default:
                                break;
                        }
                        $fee = $logisticCompanyFeeRulesResult['price'];
                        if ($jdWeight > $logisticCompanyFeeRulesResult['weight']) {
                            $continueWeight = $jdWeight - $logisticCompanyFeeRulesResult['weight'];
                            $continueWeightRule = json_decode($logisticCompanyFeeRulesResult['continue_weight_rule'], true);
                            if (empty($continueWeightRule)) {
                                throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "，重量：" . $jdWeight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueWeight . ",没有对应的续重规则");
                            }
                            $hasContinueWeightRule = false;
                            foreach ($continueWeightRule as $value) {
                                if (($continueWeight >= $value[0] && $continueWeight < $value[1]) || ($continueWeight >= $value[0] && empty($value[1]))) {
                                    $hasContinueWeightRule = true;
                                    $fee += $value[2];
                                }
                            }
                            if (!$hasContinueWeightRule) {
                                throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "，重量：" . $jdWeight . ",首重：" . $logisticCompanyFeeRulesResult['weight'] . ",续重：" . $continueWeight . ",没有对应的续重规则");
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
                        $logisticCompanySettlementOrderDetailModel->jd_weight = $jdWeight;
                        $logisticCompanySettlementOrderDetailModel->need_receipt_amount = $fee;
                        if (!$logisticCompanySettlementOrderDetailModel->save()) {
                            throw new \Exception("仓库编码：" . $itemWarehouseCode . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "更新快递公司结算单明细失败，原因:" . Utility::arrayToString($logisticCompanySettlementOrderDetailModel->getErrors()));
                        } else {
                            DeliveryOrder::updateAll(['total_price' => $fee], ['logistic_no' => $itemLogisticNo]); //更新京东收取费用
                        }
                        echo "success\r\n";
                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\r\n";
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
    }

}
<?php

namespace console\controllers;

use backend\models\CustomerAreaDeliveryFee;
use backend\models\Institution;
use common\components\Utility;
use common\models\Cnarea;
use common\models\Customer;
use common\models\CustomerSettlementOrderDetail;
use common\models\DeliveryOrder;
use yii\console\Controller;

class CustomerSettlementOrderDetailController extends Controller
{
    /**
     * ./yii customer-settlement-order-detail/run 1 '' '' 2
     *
     * @param $logisticNo
     * @param $institutionId
     * @param $customerId
     * @param $dryRun
     * @param $startTime
     * @param $endTime
     */
    public function actionRun($dryRun = 1, $startTime = '', $endTime = '', $institutionId = '', $customerId = '', $logisticNo = '')
    {
        date_default_timezone_set("Asia/Shanghai");
        if (!empty($institutionId)) {
            $sql = "SELECT * FROM " . Institution::tableName() . " WHERE status = '" . Institution::STATUS_NORMAL . "' AND id = '" . $institutionId . "' order by id DESC";
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
                if (!empty($customerId)) {
                    $deliveryOrderSql = "SELECT * FROM delivery_order where finish_time >= '" . $startTime . "' AND finish_time <= '" . $endTime . "' and institution_id = '" . $institutionId . "' AND customer_id = '" . $customerId . "' AND status in (" . DeliveryOrder::STATUS_LOST . "," . DeliveryOrder::STATUS_REJECT . "," . DeliveryOrder::STATUS_DELIVERED . ")";
                } else {
                    $deliveryOrderSql = "SELECT * FROM delivery_order where finish_time >= '" . $startTime . "' AND finish_time <= '" . $endTime . "' and institution_id = '" . $institutionId . "' AND status in (" . DeliveryOrder::STATUS_LOST . "," . DeliveryOrder::STATUS_REJECT . "," . DeliveryOrder::STATUS_DELIVERED . ")";
                }
                if (!empty($logisticNo)) {
                    $deliveryOrderSql .= " AND logistic_no = '" . $logisticNo . "' ";
                }
                $deliveryOrderSql .= " order by id desc";
                echo "deliveryOrderSql:" . $deliveryOrderSql . "\r\n";
                $deliveryOrderResult = \Yii::$app->db->createCommand($deliveryOrderSql)->queryAll();
                if (empty($deliveryOrderResult)) {
                    throw new \Exception("组织机构ID：" . $institutionId . "没有需要处理的运单。");
                }
                echo "组织机构ID：" . $institutionId . "有:" . count($deliveryOrderResult) . "条运单需要处理\r\n";
                foreach ($deliveryOrderResult as $deliveryOrder) {
                    try {
                        $itemLogisticNo = $deliveryOrder['logistic_no'];
                        $itemCustomerId = $deliveryOrder['customer_id'];
                        $orderStatus = $deliveryOrder['status'];

                        $province = Cnarea::getCodeByName($deliveryOrder['province']);
                        $city = Cnarea::getCodeByName($deliveryOrder['city']);
                        $district = Cnarea::getCodeByName($deliveryOrder['district']);
                        $parentIdList = [];
                        echo "运单信息——组织机构ID：" . $institutionId . "，客户ID：" . $itemCustomerId . "，快递单号：" . $itemLogisticNo . "，省:" . $deliveryOrder['province'] . "，市:" . $deliveryOrder['city'] . "，区县:" . $deliveryOrder['district'] . "，设备推送重量:" . $deliveryOrder['device_weight'] . "，重量:" . $deliveryOrder['weight'] . "\r\n";

                        $customerAreaDeliveryFeeList = [
                            0 => [
                                'institution_id' => $institutionId,
                                'customer_id' => $itemCustomerId,
                            ]
                        ];
                        $parentList = CustomerAreaDeliveryFee::getParents($institutionId, $parentIdList);

                        if (!empty($parentList)) {
                            foreach ($parentList as $key => $parent) {
                                if ($parent['parent_id'] == 0) {
                                    continue;
                                }
                                $parentCustomerId = Customer::getIdByName($parent['name']);
                                $parentList[$key]['customer_id'] = $parentCustomerId;
                                $customerSettlementOrder = [
                                    'institution_id' => $parent['parent_id'],
                                    'customer_id' => $parentCustomerId,
                                ];
                                array_push($customerAreaDeliveryFeeList, $customerSettlementOrder);
                            }
                        }
                        foreach ($customerAreaDeliveryFeeList as $customerAreaDeliveryFee) {
                            $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $customerAreaDeliveryFee['institution_id'] . "' AND customer_id = '" . $customerAreaDeliveryFee['customer_id'] . "' AND province = '" . $province . "' AND city = '" . $city . "' AND district = '" . $district . "'";
                            echo "customerAreaDeliveryFeeSql:" . $customerAreaDeliveryFeeSql . "\r\n";
                            $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryOne();
                            if (empty($customerAreaDeliveryFeeResult)) {
                                $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $customerAreaDeliveryFee['institution_id'] . "' AND customer_id = '" . $customerAreaDeliveryFee['customer_id'] . "' AND province = '" . $province . "' AND city = '" . $city . "'";
                                echo "customerAreaDeliveryFeeSql:" . $customerAreaDeliveryFeeSql . "\r\n";
                                $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryOne();
                                if (empty($customerAreaDeliveryFeeResult)) {
                                    $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $customerAreaDeliveryFee['institution_id'] . "' AND customer_id = '" . $customerAreaDeliveryFee['customer_id'] . "' AND province = '" . $province . "'";
                                    echo "customerAreaDeliveryFeeSql:" . $customerAreaDeliveryFeeSql . "\r\n";
                                    $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryOne();
                                    if (empty($customerAreaDeliveryFeeResult)) {
                                        throw new \Exception("组织机构ID：" . $customerAreaDeliveryFee['institution_id'] . ",客户ID：" . $customerAreaDeliveryFee['customer_id'] . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $itemLogisticNo . "对应的没有配置运费规则");
                                    }
                                }
                            }
                            $sizeWeight = DeliveryOrder::getSizeWeight($deliveryOrder['device_size'], $deliveryOrder['device_weight']);
                            $weight = max($deliveryOrder['weight'], $deliveryOrder['device_weight']);
                            $weight = max($sizeWeight, $weight);
                            $weight = ceil($weight);
                            $feeType = $customerAreaDeliveryFeeResult['fee_type'];
                            $feeRules = $customerAreaDeliveryFeeResult['fee_rules'];
                            if (empty($feeRules)) {
                                throw new \Exception("组织机构ID：" . $customerAreaDeliveryFee['institution_id'] . ",客户ID：" . $customerAreaDeliveryFee['customer_id'] . ",省：" . $deliveryOrder['province'] . ",市：" . $deliveryOrder['city'] . ",区/县：" . $deliveryOrder['district'] . "快递单号：" . $itemLogisticNo . "对应的运费规则为空");
                            }
                            $fee = 0.00;
                            $feeRules = json_decode($feeRules, true);
                            if ($feeType == CustomerAreaDeliveryFee::FEE_TYPE_FIRST_WEIGHT_AND_FOLLOW) {
                                if ($weight > $feeRules['weight']) {
                                    $fee = $feeRules['weight'] * $feeRules['price'] + ($weight - $feeRules['weight']) / $feeRules['follow_weight'] * $feeRules['follow_price'];
                                } else {
                                    $fee = $weight * $feeRules['price'];
                                }
                            } else {
                                switch ($weight) {
                                    case 0 < $weight && $weight <= 1 :
                                        $fee = $feeRules['first_weight_range_price'];
                                        break;
                                    case 1 < $weight && $weight <= 2 :
                                        $fee = $feeRules['sec_weight_range_price'];
                                        break;
                                    case 2 < $weight && $weight <= 3 :
                                        $fee = $feeRules['third_weight_range_price'];
                                        break;
                                    case 3 < $weight && $weight <= 10 :
                                        $floatFee = $feeRules['fourth_weight_range_price_float'] * $weight;
                                        $fee = $feeRules['fourth_weight_range_price'] + $floatFee;
                                        break;
                                    case 10 < $weight :
                                        $floatFee = $feeRules['fifth_weight_range_price_float'] * $weight;
                                        $fee = $feeRules['fifth_weight_range_price'] + $floatFee;
                                        break;
                                    default:
                                        break;
                                }
                            }
                            $fee += $customerAreaDeliveryFeeResult['face_order_fee']; //面单价格

                            //退货费
                            if ($orderStatus == DeliveryOrder::STATUS_LOST) {
                                $returnFee = $customerAreaDeliveryFeeResult['return_fee'] + $customerAreaDeliveryFeeResult['return_base'] * $weight;
                                $fee += $returnFee;
                            }

                            if (!$dryRun) {
                                $customerSettlementOrderDetail = CustomerSettlementOrderDetail::findOne(['logistic_no' => $itemLogisticNo, 'institution_id'=> $customerAreaDeliveryFee['institution_id'], 'customer_id' => $customerAreaDeliveryFee['customer_id']]);
                                if (!$customerSettlementOrderDetail) {
                                    $customerSettlementOrderDetail = new CustomerSettlementOrderDetail();
                                    $customerSettlementOrderDetail->logistic_no = $itemLogisticNo;
                                    $customerSettlementOrderDetail->institution_id = $customerAreaDeliveryFee['institution_id'];
                                    $customerSettlementOrderDetail->customer_id = $customerAreaDeliveryFee['customer_id'];
                                    $customerSettlementOrderDetail->province = $province;
                                    $customerSettlementOrderDetail->city = $city;
                                    $customerSettlementOrderDetail->district = $district;
                                    $customerSettlementOrderDetail->weight = $weight;
                                    $customerSettlementOrderDetail->need_receipt_amount = $fee;
                                    $customerSettlementOrderDetail->finish_time = $deliveryOrder['finish_time'];
                                    $customerSettlementOrderDetail->create_time = date('Y-m-d H:i:s', time());
                                } else {
                                    if ($customerSettlementOrderDetail->need_receipt_amount !=                                     $fee) {
                                        $customerSettlementOrderDetail->need_receipt_amount = $fee;
                                    }
                                    if ($customerSettlementOrderDetail->weight !=                                     $weight) {
                                        $customerSettlementOrderDetail->weight = $weight;
                                    }
                                }

                                if (!$customerSettlementOrderDetail->save()) {
                                    throw new \Exception(Utility::arrayToString($customerSettlementOrderDetail->getErrors()));
                                }
                            }

                            echo "组织机构ID：" . $customerAreaDeliveryFee['institution_id'] . ",客户ID：：" . $customerAreaDeliveryFee['customer_id'] . ",省：" . $deliveryOrder['province'] . ",市：" . $deliveryOrder['city'] . ",区/县：" . $deliveryOrder['district'] . "快递单号：" . $itemLogisticNo . ",重量：" . ceil($weight) . "，运费：" . $fee . ",已生成结算明细\r\n";
                        }


                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\r\n";
                    }
                }

            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
            echo "end generate settlement details\r\n";

        }
    }
}
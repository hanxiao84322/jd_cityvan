<?php

namespace common\models;

use backend\models\CustomerAreaDeliveryFee;
use common\components\Utility;
use Yii;

/**
 * This is the model class for table "customer_settlement_order_detail".
 *
 * @property int $id
 * @property string|null $settlement_order_no 结算单号
 * @property string|null $logistic_no 快递单号
 * @property int|null $institution_id 组织机构ID
 * @property int|null $customer_id 客户ID
 * @property string|null $province 省
 * @property string|null $city 市
 * @property string|null $district 区/县
 * @property float|null $weight 重量千克
 * @property string|null $size 体积
 * @property float|null $size_weight 体积重量千克
 * @property float|null $need_receipt_amount 应收金额元
 * @property string|null $finish_time 到达最终状态时间 妥投时间 拒收时间 丢失时间
 * @property string|null $create_time 创建时间
 */
class CustomerSettlementOrderDetail extends \yii\db\ActiveRecord
{
    public $sender_name;
    public $sender_phone;
    public $sender_company;
    public $sender_address;
    public $customer_name;
    public $institution_name;
    public $order_status;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_settlement_order_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'customer_id'], 'integer'],
            [['weight', 'need_receipt_amount', 'size_weight'], 'number'],
            [['create_time', 'finish_time'], 'safe'],
            [['logistic_no', 'province', 'city', 'district', 'settlement_order_no', 'size'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settlement_order_no' => '结算单号',
            'logistic_no' => '快递单号',
            'order_status' => '运单状态',
            'institution_name' => '组织机构',
            'customer_name' => '客户',
            'sender_name' => '寄件人姓名',
            'sender_phone' => '寄件人联系电话',
            'sender_company' => '寄件人公司',
            'sender_address' => '寄件人地址',
            'province' => '省',
            'city' => '市',
            'district' => '区县',
            'weight' => '重量',
            'size' => '体积',
            'size_weight' => '体积重量',
            'finish_time' => '到达最终状态时间',
            'need_receipt_amount' => '应收金额',
            'create_time' => '创建时间',
        ];
    }

    public static function batchAdd($institutionId, $customerId = '', $startTime = '', $endTime = '')
    {
        $return = [
            'success' => 0,
            'msg' => '',
            'errorList' => []
        ];
        try {
            if (!empty($customerId)) {
                $deliveryOrderSql = "SELECT * FROM delivery_order where create_time >= '" . $startTime . "' AND create_time <= '" . $endTime . "' AND institution_id = '" . $institutionId . "' AND customer_id = '" . $customerId . "' AND status in (" . DeliveryOrder::STATUS_LOST . "," . DeliveryOrder::STATUS_REJECT . "," . DeliveryOrder::STATUS_DELIVERED . ")";
            } else {
                $deliveryOrderSql = "SELECT * FROM delivery_order where create_time >= '" . $startTime . "' AND create_time <= '" . $endTime . "' AND institution_id = '" . $institutionId . "' AND status in (" . DeliveryOrder::STATUS_LOST . "," . DeliveryOrder::STATUS_REJECT . "," . DeliveryOrder::STATUS_DELIVERED . ")";
            }
            $deliveryOrderSql .= " order by id desc";
            $deliveryOrderResult = \Yii::$app->db->createCommand($deliveryOrderSql)->queryAll();
            if (empty($deliveryOrderResult)) {
                throw new \Exception("组织机构ID：" . $institutionId . "没有需要处理的运单。");
            }
            foreach ($deliveryOrderResult as $deliveryOrder) {
                try {
                    $logisticNo = $deliveryOrder['logistic_no'];
                    $customerId = $deliveryOrder['customer_id'];

                    $province = Cnarea::getCodeByName($deliveryOrder['province']);
                    $city = Cnarea::getCodeByName($deliveryOrder['city']);
                    $district = Cnarea::getCodeByName($deliveryOrder['district']);
                    $parentIdList = [];

                    $customerAreaDeliveryFeeList = [
                        0 => [
                            'institution_id' => $institutionId,
                            'customer_id' => $customerId,
                        ]
                    ];
                    $parentList = CustomerAreaDeliveryFee::getParents($institutionId, $parentIdList);

                    if (!empty($parentList)) {
                        foreach ($parentList as $key => $parent) {
                            if ($parent['parent_id'] == 0) {
                                continue;
                            }
                            $customerId = Customer::getIdByName($parent['name']);
                            $parentList[$key]['customer_id'] = $customerId;
                            $customerSettlementOrder = [
                                'institution_id' => $parent['parent_id'],
                                'customer_id' => $customerId,
                            ];
                            array_push($customerAreaDeliveryFeeList, $customerSettlementOrder);
                        }
                    }
                    foreach ($customerAreaDeliveryFeeList as $customerAreaDeliveryFee) {
                        $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $customerAreaDeliveryFee['institution_id'] . "' AND customer_id = '" . $customerAreaDeliveryFee['customer_id'] . "' AND province = '" . $province . "' AND city = '" . $city . "' AND district = '" . $district . "'";
                        $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryOne();
                        if (empty($customerAreaDeliveryFeeResult)) {
                            $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $customerAreaDeliveryFee['institution_id'] . "' AND customer_id = '" . $customerAreaDeliveryFee['customer_id'] . "' AND province = '" . $province . "' AND city = '" . $city . "'";
                            $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryOne();
                            if (empty($customerAreaDeliveryFeeResult)) {
                                $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $customerAreaDeliveryFee['institution_id'] . "' AND customer_id = '" . $customerAreaDeliveryFee['customer_id'] . "' AND province = '" . $province . "'";
                                $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryOne();
                                if (empty($customerAreaDeliveryFeeResult)) {
                                    throw new \Exception("组织机构ID：" . $customerAreaDeliveryFee['institution_id'] . ",客户ID：" . $customerAreaDeliveryFee['customer_id'] . ",省：" . $province . ",市：" . $city . ",区/县：" . $district . "快递单号：" . $logisticNo . "对应的没有配置运费规则");
                                }
                            }
                        }
                        $weight = $deliveryOrder['weight'] > $deliveryOrder['device_weight'] ? $deliveryOrder['weight'] : $deliveryOrder['device_weight'];
                        $feeType = $customerAreaDeliveryFeeResult['fee_type'];
                        $feeRules = $customerAreaDeliveryFeeResult['fee_rules'];
                        if (empty($feeRules)) {
                            throw new \Exception("组织机构ID：" . $customerAreaDeliveryFee['institution_id'] . ",客户ID：" . $customerAreaDeliveryFee['customer_id'] . ",省：" . $deliveryOrder['province'] . ",市：" . $deliveryOrder['city'] . ",区/县：" . $deliveryOrder['district'] . "快递单号：" . $logisticNo . "对应的运费规则为空");
                        }
                        $fee = 0.00;
                        $feeRules = json_decode($feeRules, true);
                        if ($feeType == CustomerAreaDeliveryFee::FEE_TYPE_FIRST_WEIGHT_AND_FOLLOW) {
                            if ($weight > $feeRules['weight']) {
                                $fee = $feeRules['weight'] * $feeRules['price'] + (ceil($weight) - $feeRules['weight']) / $feeRules['follow_weight'] * $feeRules['follow_price'];
                            } else {
                                $fee = ceil($weight) * $feeRules['price'];
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
                                    $fee = $feeRules['fourth_weight_range_price'];
                                    break;
                                case 10 < $weight :
                                    $fee = $feeRules['fifth_weight_range_price'];
                                    break;
                                default:
                                    break;
                            }
                        }
                        if (!CustomerSettlementOrderDetail::find()->where(['customer_id' => $customerAreaDeliveryFee['customer_id'], 'logistic_no' => $logisticNo])->exists()) {
                            $customerSettlementOrderDetail = new CustomerSettlementOrderDetail();
                            $customerSettlementOrderDetail->logistic_no = $logisticNo;
                            $customerSettlementOrderDetail->institution_id = $customerAreaDeliveryFee['institution_id'];
                            $customerSettlementOrderDetail->customer_id = $customerAreaDeliveryFee['customer_id'];
                            $customerSettlementOrderDetail->province = $province;
                            $customerSettlementOrderDetail->city = $city;
                            $customerSettlementOrderDetail->district = $district;
                            $customerSettlementOrderDetail->weight = $weight;
                            $customerSettlementOrderDetail->need_receipt_amount = $fee;
                            $customerSettlementOrderDetail->create_time = date('Y-m-d H:i:s', time());
                            if (!$customerSettlementOrderDetail->save()) {
                                throw new \Exception(Utility::arrayToString($customerSettlementOrderDetail->getErrors()));
                            }
                        }

                    }

                } catch (\Exception $e) {
                    $return['errorList'][] = $e->getMessage();
                }
            }

        } catch (\Exception $e) {
            $return['errorList'][] = $e->getMessage();
        }
        if (empty($return['errorList'])) {
            $return['success'] = 1;
        }
        return $return;
    }
}

<?php

namespace common\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "logistic_company_check_bill_detail".
 *
 * @property int $id
 * @property string $logistic_company_check_bill_no 对账单号
 * @property string $order_type 对账单类型 1 付费 2 收费
 * @property string $warehouse_code 仓库编码
 * @property int $logistic_id 快递公司ID
 * @property string $logistic_no 快递单号
 * @property string $order_no 订单号
 * @property float $weight 重量
 * @property float $price 应付金额
 * @property float $system_weight 系统重量
 * @property float $system_price 系统金额
 * @property int $status 状态 1 一致 2 不存在 3 金额差异
 * @property string $note 备注
 * @property string $create_username 创建人用户名
 * @property string $create_time 创建时间
 */
class LogisticCompanyCheckBillDetail extends \yii\db\ActiveRecord
{
    public $logistic_company_name;

    const STATUS_SAME = 1;
    const STATUS_NOT_FOUND = 2;
    const STATUS_PRICE_DIFF = 3;
    const STATUS_EXISTS = 4;
    const STATUS_SYSTEM_NOT_SETTLEMENT = 5;
    const STATUS_ORDER_NOT_FINISHED = 6;
    const STATUS_WEIGHT_DIFF = 7;
    public static $statusList = [
        self::STATUS_SAME => '一致',
        self::STATUS_NOT_FOUND => '不存在',
        self::STATUS_PRICE_DIFF => '金额差异',
        self::STATUS_EXISTS => '已存在',
        self::STATUS_SYSTEM_NOT_SETTLEMENT => '订单未结算',
        self::STATUS_ORDER_NOT_FINISHED => '订单未完成',
        self::STATUS_WEIGHT_DIFF => '金额与重量差异',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_check_bill_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_code', 'logistic_id',  'weight', 'price', 'status', 'create_username', 'create_time'], 'required'],
            [['logistic_id', 'status', 'order_type'], 'integer'],
            [['weight', 'price', 'system_weight', 'system_price'], 'number'],
            [['note'], 'string'],
            [['create_time'], 'safe'],
            [['logistic_company_check_bill_no', 'logistic_no', 'create_username', 'order_no'], 'string', 'max' => 50],
            [['warehouse_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistic_company_check_bill_no' => '对账单号',
            'order_type' => '对账单类型',
            'warehouse_code' => '仓库编码',
            'logistic_id' => '快递公司',
            'logistic_company_name' => '快递公司',
            'order_no' => '订单号',
            'logistic_no' => '快递单号',
            'weight' => '导入重量',
            'price' => '导入金额',
            'system_weight' => '系统重量',
            'system_price' => '系统金额',
            'status' => '状态',
            'note' => '备注',
            'create_username' => '创建人用户名',
            'create_time' => '创建时间',
        ];
    }

    public static function batchUpdate($excelData, $type, $username)
    {
        $return = [
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => '',
        ];
        $errorList = [];

        $logisticIdCheckBillList = [];
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
                $deliveryOrderModel = DeliveryOrder::findOne(['logistic_id' => $logisticId, 'warehouse_code' => $warehouseCode , 'logistic_no' => $logisticNo]);
                if (!$deliveryOrderModel) {
                    $status = LogisticCompanyCheckBillDetail::STATUS_NOT_FOUND;
                }

                if (!in_array($deliveryOrderModel->status, [DeliveryOrder::STATUS_DELIVERED, DeliveryOrder::STATUS_REPLACE_DELIVERED, DeliveryOrder::STATUS_REJECT_IN_WAREHOUSE])) {
                    $note .= "订单状态是" . DeliveryOrder::getStatusName($deliveryOrderModel->status) . "未达到最终状态！\r\n";
                }
                $systemWeight = '';
                $systemPrice = '';
                $logisticCompanySettlementOrderDetailModel = LogisticCompanySettlementOrderDetail::findOne(['logistic_no' => $logisticNo]);
                if (!$logisticCompanySettlementOrderDetailModel) {
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
                if ($logisticCompanyCheckBillDetailExists) {
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
                $logisticCompanyCheckBillDetailModel->create_username = $username;
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

            } catch (\Exception $e) {
                $return['errorCount']++;
                $errorList[] = '第' . $line . '行失败，' . $e->getMessage();
                $return['errorList'] = $errorList;
            }
        }
        if ($return['errorCount'] == 0) {
            $return = [
                'successCount' => 0,
                'errorCount' => 0,
                'errorList' => '',
            ];
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
                                $logisticCompanyCheckBillModel->create_username = $username;
                                $logisticCompanyCheckBillModel->create_time = date('Y-m-d H:i:s', time());
                                $logisticCompanyCheckBillModel->status = LogisticCompanyCheckBill::STATUS_NEW;
                                $logisticCompanyCheckBillModel->type = $type;
                                if (!$logisticCompanyCheckBillModel->save()) {
                                    throw new \Exception(Utility::arrayToString($logisticCompanyCheckBillModel->getErrors()));
                                }
                                LogisticCompanyCheckBillDetail::updateAll(['logistic_company_check_bill_no' => $logisticCompanyCheckBillModel->logistic_company_check_bill_no], ['in', 'id' , $checkBill['detailIdList']]);
                                $return['successCount']++;
                            } catch (\Exception $e) {
                                $return['errorCount']++;
                                $errorList[] = '快递公司id：' . $logisticId . "，仓库编码：" . $warehouseCode . ",创建对账单失败，原因："  . $e->getMessage();
                                $return['errorList'] = $errorList;
                            }
                        }
                    } catch (\Exception $e) {
                        $return['errorCount']++;
                        $errorList[] = '快递公司id：' . $logisticId . "创建对账单失败，原因："  . $e->getMessage();
                        $return['errorList'] = $errorList;
                    }

                }

            }
        }
        return $return;
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

}

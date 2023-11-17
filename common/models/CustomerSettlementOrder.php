<?php

namespace common\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "customer_settlement_order".
 *
 * @property int $id 自增ID
 * @property string|null $settlement_order_no 客户结算单号
 * @property int|null $institution_id 组织机构ID
 * @property int $customer_id 客户ID
 * @property float $need_receipt_amount 应收金额 运费
 * @property float $need_pay_amount 应付金额 延误，破损，拒收
 * @property float|null $adjust_amount 调整金额
 * @property float $need_amount 应结算金额  应收-应付+调整金额
 * @property string $start_time 结算周期开始时间
 * @property string $end_time 结算周期结束时间
 * @property int $status 状态 01 待确认 02 已确认 03 已付款
 * @property string $pay_image_path 支付凭证图片地址
 * @property string|null $create_name 客户结算单号
 * @property string $create_time 更新时间
 * @property string|null $update_name 客户结算单号
 * @property string $update_time 更新时间
 */
class CustomerSettlementOrder extends \yii\db\ActiveRecord
{

    public $customer_name;
    public $institution_name;
    public $logistic_no;

    const STATUS_WAIT = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_PAID = 3;
    public static $statusList = [
        self::STATUS_WAIT => '待确认',
        self::STATUS_CONFIRM => '已确认',
        self::STATUS_PAID => '已付款',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_settlement_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'customer_id', 'status'], 'integer'],
            [['institution_id', 'customer_id', 'need_receipt_amount', 'need_amount', 'start_time', 'end_time', 'status'], 'required'],
            [['need_receipt_amount', 'need_pay_amount', 'adjust_amount', 'need_amount'], 'number'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['settlement_order_no', 'create_name', 'update_name'], 'string', 'max' => 20],
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
            'institution_name' => '组织机构',
            'customer_name' => '客户',
            'institution_id' => '组织机构',
            'customer_id' => '客户',
            'need_receipt_amount' => '应收金额',
            'need_pay_amount' => '应付金额',
            'adjust_amount' => '调整金额',
            'need_amount' => '实收金额',
            'start_time' => '账期开始时间',
            'end_time' => '账期结束时间',
            'status' => '状态',
            'pay_image_path' => '支付凭证',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '修改人用户名',
            'update_time' => '修改时间',
        ];
    }

    public static function generateId()
    {
        $settlementOrderNo = '';
        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        $p = "SO" . date('ymd');
        if ($lasted && strstr($lasted['settlement_order_no'], $p)) {
            $last_id = $lasted['settlement_order_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        $settlementOrderNo = $p . str_pad(++$count, 4, '0', STR_PAD_LEFT);
        return $settlementOrderNo;
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function add($institutionId, $customerId = '', $startTime = '', $endTime = '')
    {
        $return = [
            'success' => 0,
            'msg' => '',
            'errorList' => []
        ];
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $customerSettlementOrderDetailSql = "SELECT * FROM " . CustomerSettlementOrderDetail::tableName() . " WHERE create_time >= '" . $startTime . "' AND  create_time <= '" . $endTime . "' AND institution_id = " . $institutionId . " ";
            if (!empty($customerId)) {
                $customerSettlementOrderDetailSql .= " and  customer_id = '" . $customerId . "' ";
            }
            $customerSettlementOrderDetailSql .= " ORDER BY create_time DESC";

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
                    $settlementOrderNo = CustomerSettlementOrder::generateId();
                    $customerSettlementOrderModel = new CustomerSettlementOrder();
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
                    if (!$customerSettlementOrderModel->save()) {
                        throw new \Exception(Utility::arrayToString($customerSettlementOrderModel->getErrors()));
                    }
                    if (!empty($detailCustomerIdMap)) {
                        CustomerSettlementOrderDetail::updateAll(['settlement_order_no' => $settlementOrderNo], ['in', 'id', $detailCustomerIdMap[$customerIdKey]]);
                    }
                    if (!empty($logisticNoIsSettlementMap)) {
                        DeliveryOrder::updateAll(['is_customer_settle' => DeliveryOrder::YES], ['in', 'logistic_no', $logisticNoIsSettlementMap[$customerIdKey]]);
                    }
                    $transaction->commit();
                }
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            $return['msg'] = $e->getMessage();
        }

        return $return;
    }
}

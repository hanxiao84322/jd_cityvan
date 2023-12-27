<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_settlement_order_detail".
 *
 * @property int $id
 * @property string|null $settlement_order_no 结算单号
 * @property string|null $logistic_no 快递单号
 * @property string|null $order_no 订单号
 * @property string|null $warehouse_code 仓库编码
 * @property int|null $logistic_id 快递公司ID
 * @property string|null $province 省
 * @property string|null $city 市
 * @property string|null $district 区/县
 * @property float|null $weight 重量千克
 * @property float|null $jd_weight 京东结算重量千克
 * @property string|null $size 体积
 * @property float|null $size_weight 体积重量
 * @property float|null $need_pay_amount 应付金额元
 * @property float|null $need_receipt_amount 应收金额元
 * @property float|null $jd_order_weight 京东结算重量千克订单维度
 * @property float|null $order_need_receipt_amount 京东结算应收金额元订单维度
 * @property float|null $order_split_shipping_need_receipt_amount 京东结算订单维度均分后快递应收金额元
 * @property string $finish_time 到达最终状态时间 妥投时间 拒收时间 丢失时间
 * @property string|null $create_time 创建时间
 */
class LogisticCompanySettlementOrderDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_settlement_order_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_id'], 'integer'],
            [['weight', 'size_weight', 'need_receipt_amount', 'need_pay_amount', 'jd_weight', 'jd_order_weight', 'order_need_receipt_amount', 'order_split_shipping_need_receipt_amount'], 'number'],
            [['finish_time', 'create_time'], 'safe'],
            [['settlement_order_no', 'logistic_no','order_no', 'warehouse_code', 'province', 'city', 'district', 'size'], 'string', 'max' => 50],
            [['logistic_no'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settlement_order_no' => 'Settlement Order No',
            'logistic_no' => 'Logistic No',
            'warehouse_code' => 'Warehouse Code',
            'logistic_id' => 'Logistic ID',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'weight' => 'Weight',
            'size' => 'Size',
            'size_weight' => 'Size Weight',
            'need_receipt_amount' => 'Need Receipt Amount',
            'finish_time' => 'Finish Time',
            'create_time' => 'Create Time',
        ];
    }

    public static function getSplitAmount($orderFee, $orderNo)
    {
        $splitAmount = $orderFee;
        $orderCount = self::find()->where(['order_no' => $orderNo])->count();
        if ($orderCount > 0) {
            $splitAmount = round(($orderFee / $orderCount), 2);
        }
        return $splitAmount;
    }
}

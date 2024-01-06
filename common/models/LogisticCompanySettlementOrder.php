<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_settlement_order".
 *
 * @property int $id 自增ID
 * @property string|null $settlement_order_no 结算单号
 * @property string $logistic_company_check_bill_no 对账单号
 * @property string $warehouse_code 仓库编码
 * @property int|null $logistic_id 快递公司ID
 * @property int $type 1 付费结算单 2 收款结算单
 * @property string $date 生成日期
 * @property int $order_num 订单数量
 * @property float|null $need_receipt_amount 应收金额 运费
 * @property float|null $need_pay_amount 应付金额 延误，破损，拒收
 * @property float|null $adjust_amount 调整金额
 * @property float|null $preferential_amount 优惠金额
 * @property float|null $need_amount 应结算金额  应收-应付+调整金额
 * @property string|null $start_time 结算周期开始时间
 * @property string|null $end_time 结算周期结束时间
 * @property int|null $status 状态 01 待确认 02 已确认 03 已付款
 * @property int $diff_adjust_plan 差异调整方案 1 以系统为准 2 手动调整
 * @property float|null $input_amount 手动输入的金额
 * @property int $discounts_reductions 折扣方案
 * @property int $adjust_term 调整项
 * @property string|null $pay_image_path 支付凭证图片地址
 * @property string $note 备注
 * @property string|null $create_name 创建人姓名
 * @property string|null $create_time 创建时间
 * @property string|null $update_name 更新人姓名
 * @property string|null $update_time 更新时间
 */
class LogisticCompanySettlementOrder extends \yii\db\ActiveRecord
{
    public $expect_amount;
    public $logistic_company_name;

    const STATUS_WAIT = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_PAID = 3;
    public static $statusList = [
        self::STATUS_WAIT => '待确认',
        self::STATUS_CONFIRM => '已确认',
        self::STATUS_PAID => '已完成',
    ];

    const TYPE_PAY = 1;
    const TYPE_REC = 2;
    public static  $typeList = [
        self::TYPE_PAY => '付费',
        self::TYPE_REC => '收费',
    ];

    const DIFF_ADJUST_PLAN_SYSTEM = 1;
    const DIFF_ADJUST_PLAN_INPUT = 2;


    public static $diffAdjustPlanList = [
        self::DIFF_ADJUST_PLAN_SYSTEM => '以系统结算金额为准',
        self::DIFF_ADJUST_PLAN_INPUT => '手动输入结算金额',

    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_settlement_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_company_check_bill_no', 'warehouse_code', 'type', 'date', 'order_num', 'diff_adjust_plan'], 'required'],
            [['logistic_id', 'type', 'order_num', 'status', 'diff_adjust_plan', 'discounts_reductions'], 'integer'],
            [['date', 'start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['need_receipt_amount', 'need_pay_amount', 'adjust_amount', 'need_amount','input_amount', 'preferential_amount'], 'number'],
            [['settlement_order_no', 'warehouse_code', 'create_name', 'update_name'], 'string', 'max' => 20],
            [['logistic_company_check_bill_no'], 'string', 'max' => 50],
            [['pay_image_path', 'adjust_term', 'note'], 'string', 'max' => 255],
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
            'logistic_company_check_bill_no' => '对账单号',
            'warehouse_code' => '仓库编码',
            'logistic_id' => '快递公司ID',
            'logistic_company_name' => '快递公司',
            'type' => '类型',
            'date' => '生成时间',
            'order_num' => '订单数量',
            'need_receipt_amount' => '应收金额(元)',
            'need_pay_amount' => '应付金额(元)',
            'adjust_amount' => '调整总金额(元)',
            'preferential_amount' => '优惠金额(元)',
            'need_amount' => '应结算金额(元)',
            'expect_amount' => '预计金额(元)',
            'start_time' => '结算周期开始时间',
            'end_time' => '结算周期结束时间',
            'status' => '状态',
            'diff_adjust_plan' => '差异调整方案',
            'input_amount' => '输入结算金额(元)',
            'discounts_reductions' => '折扣方案',
            'pay_image_path' => '支付凭证',
            'note' => '备注',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }

    public static function generateId()
    {

        do {
            $random_number = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (substr($random_number, -1) === '0');   // 在此处，$random_number 是一个四位数，它的末尾不是 0

        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        $p = "SO" . date('ymd');
        if ($lasted && strstr($lasted['settlement_order_no'], $p)) {
            $last_id = $lasted['settlement_order_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        return $p . str_pad(++$count, 4, '0', STR_PAD_LEFT)  . $random_number;
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }

    public static function getDiffAdjustPlanListName($diffAdjustPlan)
    {
        return isset(self::$diffAdjustPlanList[$diffAdjustPlan]) ? self::$diffAdjustPlanList[$diffAdjustPlan] : '无';
    }

    public static function getAdjustTerm($adjustTerm)
    {
        $adjustTermHtml = '';
        $adjustTermArr = json_decode($adjustTerm, true);

        if (!empty($adjustTermArr)) {
            foreach ($adjustTermArr as $item) {
                $adjustTermHtml .= "调整金额：" . (!empty($item['adjust_amount']) ? $item['adjust_amount'] : 0) . "元，说明：" . (!empty($item['adjust_content']) ? $item['adjust_content'] : '无') . "<br>";
            }
        }
        return $adjustTermHtml;
    }

    public static function getUpdateAdjustTerm($adjustTerm)
    {
        $adjustTermHtml = '';
        $adjustTermArr = json_decode($adjustTerm, true);

        if (!empty($adjustTermArr)) {
            foreach ($adjustTermArr as $item) {

                $adjustTermHtml = "<div class=\"adjust-term-container\">调整金额：<input name=\"adjust_amount[]\" type=\"text\" value='" . $item['adjust_amount'] . "' class=\"adjust_amount_list\">元&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;说明：<input name=\"adjust_content[]\" type=\"text\" value='" . $item['adjust_content'] . "'  class=\"adjust_content_list\">&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"delete_adjust_term(event)\">删除</a></div>";

            }
        }
        return $adjustTermHtml;
    }

    public static function getFixedDiscount($warehouseCode, $needAmount, $settlementOrderNum)
    {
        $specifiedDate = date('Y-m-d H:i:s', time()); // 指定时间
// 获取指定时间的上一个月
        $lastMonth = strtotime('-1 month', strtotime($specifiedDate));

// 获取上一个月的第一天
        $firstDay = date('Y-m-01 00:00:00', $lastMonth);

// 获取上一个月的最后一天
        $lastDay = date('Y-m-t 23:59:59', $lastMonth);

        $orderNum = DeliveryOrder::find()->where("warehouse_code = '" . $warehouseCode . "' AND  (delivered_time >= '" . $firstDay . "' and delivered_time < '" . $lastDay . "') OR (replace_delivered_time >= '" . $firstDay . "' and replace_delivered_time < '" . $lastDay . "')")->count();

        switch ($orderNum) {
            case $orderNum >= 0 && $orderNum < 20000:
                $needAmount = $needAmount;
                break;
            case $orderNum >= 20000 && $orderNum < 30000:
                $needAmount = $needAmount - $settlementOrderNum*0.5;
                break;
            case $orderNum >= 30000:
                $needAmount = $needAmount - $settlementOrderNum*1;
                break;
            default:
                $needAmount = $needAmount;
        }
        return $needAmount;
    }
}

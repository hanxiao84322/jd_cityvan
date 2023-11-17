<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_area_delivery_fee".
 *
 * @property int $id 自增ID
 * @property int|null $logistic_id 快递公司ID
 * @property int|null $fee_type 运费类型 2 首加续 1 区间
 * @property string|null $province 目的省
 * @property string|null $city 目的市
 * @property string|null $district 目的区县
 * @property string $fee_rules 运费规则
 * @property float|null $invoice_base_price 发票基数
 * @property float|null $face_order_fee 面单费
 * @property float|null $return_fee 退货费
 * @property float|null $return_base 退货基数（元/千克）
 * @property float|null $orders_base_fee 单量基数（账单单量/账单自然日数）
 * @property float|null $under_orders_base_fee 小于单量基数费用
 * @property float|null $follow_price 续重价格元
 * @property float|null $return_rate 退货费率% 发货费用
 * @property float|null $agent_rate 代理费率% 每单
 * @property int|null $is_cancel 是否作废 0 否 1 是
 * @property string|null $create_user
 * @property string|null $create_time
 * @property string|null $update_user
 * @property string|null $update_time
 */
class LogisticAreaDeliveryFee extends \yii\db\ActiveRecord
{
    public $logistic_name   ;
    const YES = 1;
    const NO = 0;
    public static $isCancelList = [
        self::NO => '否',
        self::YES => '是',
    ];

    const FEE_TYPE_FIRST_WEIGHT_AND_FOLLOW = 2;
    const FEE_TYPE_RANGE = 1;
    public static $feeTypeList = [
        self::FEE_TYPE_FIRST_WEIGHT_AND_FOLLOW => '首重加续重',
        self::FEE_TYPE_RANGE => '重量区间',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_area_delivery_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_id', 'fee_type', 'is_cancel'], 'integer'],
            [['fee_rules'], 'required'],
            [['fee_rules'], 'string'],
            [['invoice_base_price', 'face_order_fee', 'return_fee', 'return_base', 'orders_base_fee', 'under_orders_base_fee', 'follow_price', 'return_rate', 'agent_rate'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['province', 'city', 'district'], 'string', 'max' => 255],
            [['create_user', 'update_user'], 'string', 'max' => 50],
            [['logistic_id', 'province', 'city', 'district'], 'unique', 'targetAttribute' => ['logistic_id', 'province', 'city', 'district']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistic_id' => '快递公司',
            'logistic_name' => '快递公司',
            'fee_type' => '运费类型',
            'province' => '目的省',
            'city' => '目的市',
            'district' => '目的区/县',
            'fee_rules' => '运费规则',
            'invoice_base_price' => '发票基数',
            'face_order_fee' => '面单费',
            'return_fee' => '退货费',
            'return_base' => '退货基数（元/千克）',
            'orders_base_fee' => '单量基数（账单单量/账单自然日数）',
            'under_orders_base_fee' => '小于单量基数费用',
            'follow_price' => 'Follow Price',
            'return_rate' => '退货费率% 发货费用',
            'agent_rate' => '代理费率% 每单',
            'is_cancel' => '是否作废',
            'create_user' => '创建人用户名',
            'create_time' => '创建时间',
            'update_user' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }


    public static function getIsCancelName($isCancel)
    {
        return isset(self::$isCancelList[$isCancel]) ? self::$isCancelList[$isCancel] : '无';
    }


    public static function getFeeName($feeType)
    {
        return isset(self::$feeTypeList[$feeType]) ? self::$feeTypeList[$feeType] : '无';
    }

    public static function getFeeRules($feeRules, $feeType)
    {
        $feeRulesHtml = '';
        $feeData = json_decode($feeRules, true);
        if (!empty($feeData)) {
            if ($feeType == self::FEE_TYPE_RANGE) {
                $feeRulesHtml .= '一阶0-1千克:' .
                    $feeData['first_weight_range_price'] . '元,' . '二阶1-2千克:' .
                    $feeData['sec_weight_range_price'] . '元,' . '三阶2-3千克:' .
                    $feeData['third_weight_range_price'] . '元,' . '四阶3-10千克:' .
                    $feeData['fourth_weight_range_price'] . '元<br>' . '四阶（浮动价）元/千克:' .
                    $feeData['fourth_weight_range_price_float'] . '元,' . '五阶10千克以上:' .
                    $feeData['fifth_weight_range_price'] . '元,' . '五阶（浮动价）元/千克:' .
                    $feeData['fifth_weight_range_price_float'] . '元';
            } elseif ($feeType == self::FEE_TYPE_FIRST_WEIGHT_AND_FOLLOW) {
                $feeRulesHtml .= '首重(千克):' . $feeData['weight'] . '元,' . '首重价格(元/千克):' . $feeData['price'] . '元,' . '续重(千克):' . $feeData['follow_weight'] . '元,' . '续重价格(元/千克):' . $feeData['follow_price'] . '元';
            }
        }
        return $feeRulesHtml;
    }
}

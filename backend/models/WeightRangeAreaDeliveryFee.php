<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "weight_range_area_delivery_fee".
 *
 * @property int $id 自增ID
 * @property int|null $institution_id 组织机构ID
 * @property int|null $parent_customer_id 上级客户ID
 * @property int|null $customer_id 客户ID
 * @property string|null $province 目的省
 * @property string|null $city 目的市
 * @property string|null $district 目的区县
 * @property float|null $first_weight_range_price 一阶0-1千克
 * @property float|null $sec_weight_range_price 二阶1-2千克
 * @property float|null $third_weight_range_price 三阶2-3千克
 * @property float|null $fourth_weight_range_price 四阶3-10千克
 * @property float|null $fourth_weight_range_price_float 四阶（浮动价）元/千克
 * @property float|null $fifth_weight_range_price 五阶10千克以上
 * @property float|null $fifth_weight_range_price_float 五阶（浮动价）元/千克
 * @property float|null $invoice_base_price 发票基数
 * @property float|null $face_order_fee 面单费
 * @property float|null $return_fee 退货费
 * @property float|null $return_base 退货基数（元/千克）
 * @property float|null $orders_base_fee 单量基数（账单单量/账单自然日数）
 * @property float|null $under_orders_base_fee 小于单量基数费用
 * @property float|null $return_rate 退货费率% 发货费用
 * @property float|null $agent_rate 代理费率% 每单
 * @property int|null $is_cancel 是否作废 0 否 1 是
 * @property string|null $create_user
 * @property string $create_time
 * @property string|null $update_user
 * @property string $update_time
 */
class WeightRangeAreaDeliveryFee extends \yii\db\ActiveRecord
{
    public $parent_customer_name;
    public $customer_name;
    public $customer_type;

    const YES = 1;
    const NO = 0;
    public static $isCancelList = [
        self::NO => '否',
        self::YES => '是',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'weight_range_area_delivery_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id','province', 'first_weight_range_price', 'sec_weight_range_price', 'third_weight_range_price', 'fourth_weight_range_price', 'fourth_weight_range_price_float', 'fifth_weight_range_price', 'fifth_weight_range_price_float'], 'required'],
            [['id', 'parent_customer_id', 'customer_id','institution_id', 'is_cancel'], 'integer'],
            [['first_weight_range_price', 'sec_weight_range_price', 'third_weight_range_price', 'fourth_weight_range_price', 'fourth_weight_range_price_float', 'fifth_weight_range_price', 'fifth_weight_range_price_float', 'invoice_base_price', 'face_order_fee', 'return_fee', 'return_base', 'orders_base_fee', 'under_orders_base_fee', 'return_rate', 'agent_rate'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['province', 'city', 'district'], 'string', 'max' => 255],
            [['create_user', 'update_user'], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_customer_id' => '上级客户ID',
            'institution_id' => ' 组织机构',
            'customer_id' => '客户',
            'parent_customer_name' => '上级客户',
            'customer_name' => '客户',
            'province' => '省',
            'city' => '市',
            'district' => '区/县',
            'first_weight_range_price' => '一阶0-1千克',
            'sec_weight_range_price' => '二阶1-2千克',
            'third_weight_range_price' => '三阶2-3千克',
            'fourth_weight_range_price' => '四阶3-10千克',
            'fourth_weight_range_price_float' => '四阶（浮动价）元/千克',
            'fifth_weight_range_price' => '五阶10千克以上',
            'fifth_weight_range_price_float' => '五阶（浮动价）元/千克',
            'invoice_base_price' => '发票基数',
            'face_order_fee' => '面单费',
            'return_fee' => '退货费',
            'return_base' => '退货基数（元/千克）',
            'orders_base_fee' => '单量基数（账单单量/账单自然日数）',
            'under_orders_base_fee' => '小于单量基数费用',
            'return_rate' => '退货费率',
            'agent_rate' => '代理费率',
            'is_cancel' => '是否作废',
            'create_user' => '创建人用户名',
            'create_time' => '创建时间',
            'update_user' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }
}

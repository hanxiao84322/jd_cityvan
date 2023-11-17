<?php

namespace backend\models;

use common\components\Utility;
use common\models\Customer;
use Yii;

/**
 * This is the model class for table "first_weight_and_follow_area_delivery_fee".
 *
 * @property int $id 自增ID
 * @property int|null $institution_id 组织机构ID
 * @property int|null $parent_customer_id 上级客户ID
 * @property int|null $customer_id 客户ID
 * @property int|null $fee_type 运费类型 1 首加续 2 区间
 * @property string|null $province 目的省
 * @property string|null $city 目的市
 * @property string|null $district 目的区县
 * @property float|null $weight 首重公斤
 * @property float|null $price 首重价格元
 * @property float|null $follow_weight 续重公斤
 * @property float|null $fee_rules 运费规则
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
 * @property string $create_time
 * @property string|null $update_user
 * @property string $update_time
 */
class CustomerAreaDeliveryFee extends \yii\db\ActiveRecord
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
        return 'customer_area_delivery_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['customer_id', 'unique', 'targetAttribute' => ['institution_id', 'customer_id', 'province'], 'targetClass' => '\backend\models\CustomerAreaDeliveryFee', 'message' => '客户运费规则已存在.'],
            [['customer_id', 'province', 'fee_rules'], 'required'],
            [['fee_type', 'parent_customer_id', 'customer_id', 'institution_id', 'is_cancel'], 'integer'],
            [['weight', 'price', 'follow_weight', 'invoice_base_price', 'face_order_fee', 'return_fee', 'return_base', 'orders_base_fee', 'under_orders_base_fee', 'follow_price', 'return_rate', 'agent_rate'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['province', 'city', 'district'], 'string', 'max' => 255],
            [['create_user', 'update_user'], 'string', 'max' => 50],
            [['fee_rules'], 'string'],
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
            'customer_type' => '客户类型',
            'province' => '省',
            'city' => '市',
            'district' => '区/县',
            'weight' => '首重(千克)',
            'price' => '首重价格(元/千克)',
            'follow_weight' => '续重(千克)',
            'follow_price' => '续重价格(元/千克)',
            'invoice_base_price' => '发票基数',
            'face_order_fee' => '面单费',
            'return_fee' => '退货费',
            'return_base' => '退货基数（元/千克）',
            'orders_base_fee' => '单量基数（账单单量/账单自然日数）',
            'under_orders_base_fee' => '小于单量基数费用',
            'return_rate' => '退货费率',
            'agent_rate' => '代理费率',
            'fee_rules' => '运费规则',
            'fee_type' => '运费类型',
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

    public static function createByModel($model)
    {
        $return = [
            'success' => 0,
            'msg' => ''
        ];
        try {
            $parentIdList = [];
            $parentInstitutionId = Institution::getParentIdById($model['institution_id']);
            $customerId = Customer::getIdByInstitutionId($model['institution_id']);
            $parentList = self::getParents($parentInstitutionId, $parentIdList);
            if (!empty($parentList)) {
                $deliveryFeeModel = CustomerAreaDeliveryFee::find()->where(['customer_id' => $customerId, 'institution_id' => $parentInstitutionId])->asArray()->one();
                foreach ($parentList as $parent) {
                    if (!empty($deliveryFeeModel)) {
                        $newModel = new self();
                        $newModel->attributes = $deliveryFeeModel;
                        $newModel->customer_id = $model['customer_id'];
                        $newModel->institution_id = $parent['id'];
                        if (!$newModel->save()) {
                            throw new \Exception(Utility::arrayToString($newModel->getErrors()));
                        }
                    }
                }
            }
            $return['success'] = 1;
        } catch (\Exception $e) {
            $return['msg'] = $e->getMessage();
        }

        return $return;
    }

    // 无限查询上级
    public static function getParents($pid, $arr)
    {
        $row = Institution::find()->where(['id' => $pid])->asArray()->select('id, parent_id, name')->one();
        array_push($arr, $row);
        if ($row['parent_id'] == 0) {
            return $arr;
        } else {
            return self::getParents($row['parent_id'], $arr);
        }

    }
}

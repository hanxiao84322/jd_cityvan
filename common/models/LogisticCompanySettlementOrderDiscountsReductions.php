<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_settlement_order_discounts_reductions".
 *
 * @property int $id
 * @property string $name 方案名称
 * @property int $type 类型 1 直降 2 折扣
 * @property float $min_price 最低金额
 * @property float $discount 折扣
 * @property float $sub_price 直降金额
 * @property string $content 说明
 * @property string $create_username 创建人用户名
 * @property string $create_time 创建时间
 * @property string $update_username 更新人用户名
 * @property string $update_time 更新时间
 */
class LogisticCompanySettlementOrderDiscountsReductions extends \yii\db\ActiveRecord
{
    const TYPE_SUB_PRICE = 1;
    const TYPE_RATE = 2;
    public static $typeList = [
        self::TYPE_SUB_PRICE => '直降',
        self::TYPE_RATE => '折扣',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_settlement_order_discounts_reductions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'min_price'], 'required'],
            [['type'], 'integer'],
            [['min_price', 'discount', 'sub_price'], 'number'],
            [['content'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['create_username', 'update_username'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '方案名称',
            'type' => '优惠类型',
            'min_price' => '最低金额',
            'discount' => '折扣',
            'sub_price' => '直降金额(元)',
            'content' => '说明',
            'create_time' => '创建时间',
            'create_username' => '创建人用户名',
            'update_time' => '更新时间',
            'update_username' => '更新人用户名',
        ];
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }

    public static function getAll()
    {
        return self::find()->asArray()->all();
    }

    public static function getAmount($id, $expectAmount)
    {

        $discountsReductionsAmount = $expectAmount;
        $term = self::findOne($id);
        if ($term->type == self::TYPE_SUB_PRICE) {
            if ($expectAmount >= $term->min_price) {
                $discountsReductionsAmount = $expectAmount - $term->sub_price;
            }
        } elseif ($term->type == self::TYPE_RATE) {
            if ($expectAmount >= $term->min_price) {
                $discountsReductionsAmount = $expectAmount * ($term->discount / 10);
            }
        }
        return $discountsReductionsAmount;
    }
}

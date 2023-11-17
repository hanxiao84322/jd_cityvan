<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_balance_log".
 *
 * @property int $id 自增ID
 * @property int|null $institution_id 组织机构ID
 * @property int|null $customer_id 客户ID
 * @property float|null $before_balance 变更前客户余额
 * @property float|null $after_balance 变更后客户余额
 * @property float|null $change_amount 变更金额
 * @property string|null $source 单据号
 * @property int|null $type 变更方式 1 增加 2 减少
 * @property int|null $category 变更类型 1 充值 2 结算单账扣
 * @property string|null $change_time 变更时间
 */
class CustomerBalanceLog extends \yii\db\ActiveRecord
{
    public $page_size = 20;
    const TYPE_ADD = 1;
    const TYPE_SUB = 2;
    public static $typeList = [
        self::TYPE_ADD => '增加',
        self::TYPE_SUB => '减少',
    ];

    const CATEGORY_RECHARGE = 1;
    const CATEGORY_SETTLEMENT = 2;
    public static $categoryList = [
        self::CATEGORY_RECHARGE => '充值',
        self::CATEGORY_SETTLEMENT => '结算',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_balance_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'customer_id', 'type', 'category'], 'integer'],
            [['before_balance', 'after_balance', 'change_amount'], 'number'],
            [['change_time'], 'safe'],
            [['source'], 'string', 'max' => 50],
            [['institution_id', 'customer_id', 'source'], 'unique', 'targetAttribute' => ['institution_id', 'customer_id', 'source']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institution_id' => 'Institution ID',
            'customer_id' => 'Customer ID',
            'before_balance' => '变更前客户余额',
            'after_balance' => '变更后客户余额',
            'change_amount' => '变更金额',
            'source' => '单据号',
            'type' => '变更方式',
            'category' => '变更类型',
            'change_time' => '变更时间',
        ];
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }

    public static function getCategoryName($category)
    {
        return isset(self::$categoryList[$category]) ? self::$categoryList[$category] : '无';
    }

}

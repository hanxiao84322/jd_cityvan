<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_balance".
 *
 * @property int $id 自增ID
 * @property int|null $institution_id 组织机构ID
 * @property int|null $customer_id 客户ID
 * @property int|null $face_orders_num 预计可用单号数
 * @property float|null $balance 预缴费用余额
 * @property string|null $last_recharge_time 最近一次充值时间
 * @property string|null $default_recharge_username 默认充值人
 * @property string|null $last_operation_detail 最后操作明细
 * @property string|null $last_recharge_notes 最近一次充值备注
 * @property string|null $update_username 更新人用户名
 * @property string|null $update_time 更新时间
 */
class CustomerBalance extends \yii\db\ActiveRecord
{

    public $customer_name = '';
    public $customer_type = '';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'customer_id', 'face_orders_num'], 'integer'],
            [['balance'], 'number'],
            [['last_recharge_time', 'update_time'], 'safe'],
            [['default_recharge_username', 'update_username'], 'string', 'max' => 50],
            [['last_operation_detail', 'last_recharge_notes'], 'string', 'max' => 255],
            [['institution_id', 'customer_id'], 'unique', 'targetAttribute' => ['institution_id', 'customer_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institution_id' => '组织机构',
            'customer_id' => '客户名称',
            'customer_name' => '客户名称',
            'face_orders_num' => '预计可用单号数',
            'balance' => '预缴费用余额',
            'last_recharge_time' => '最近一次充值时间',
            'default_recharge_username' => '默认充值人',
            'last_operation_detail' => '最后操作明细',
            'last_recharge_notes' => '最近一次充值备注',
            'update_username' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }
}

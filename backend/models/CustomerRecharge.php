<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "customer_recharge".
 *
 * @property int $id 自增ID
 * @property string|null $recharge_order_no 充值单号
 * @property int|null $institution_id 组织机构ID
 * @property int|null $customer_id 客户ID
 * @property float|null $amount 充值金额
 * @property int|null $type 充值方式 1 在线充值 2 线下充值
 * @property int|null $status 状态 0 支付中 1 已支付
 * @property string|null $notes 备注
 * @property string|null $pay_image_path 支付凭证图片地址
 * @property string|null $invoice_image_path 发票图片地址
 * @property string|null $create_name 创建人用户名
 * @property string|null $create_time 创建时间
 * @property string|null $update_name 更新人用户名
 * @property string|null $update_time 更新时间
 * @property string|null $pay_confirm_name 支付确认人用户名
 * @property string|null $pay_confirm_time 支付确认时间
 */
class CustomerRecharge extends \yii\db\ActiveRecord
{
    public  $customer_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_recharge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'customer_id', 'type', 'status'], 'integer'],
            [['amount'], 'number'],
            [['create_time', 'update_time', 'pay_confirm_time'], 'safe'],
            [['recharge_order_no', 'create_name', 'update_name', 'pay_confirm_name'], 'string', 'max' => 20],
            [['notes', 'pay_image_path', 'invoice_image_path'], 'string', 'max' => 255],
            [['institution_id', 'customer_id', 'recharge_order_no'], 'unique', 'targetAttribute' => ['institution_id', 'customer_id', 'recharge_order_no']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recharge_order_no' => '充值单号',
            'institution_id' => '组织机构',
            'customer_id' => '客户',
            'customer_name' => '客户',
            'amount' => '充值金额',
            'type' => '充值方式',
            'status' => '状态',
            'notes' => '备注',
            'pay_image_path' => '支付凭证图片地址',
            'invoice_image_path' => '发票图片地址',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '更新人用户名',
            'update_time' => '更新时间',
            'pay_confirm_name' => '支付确认人用户名',
            'pay_confirm_time' => '支付确认时间',
        ];
    }

    public static function generateId()
    {
        $rechargeOrderNo = '';
        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        $p = "RO" . date('ymd');
        if ($lasted && strstr($lasted['recharge_order_no'], $p)) {
            $last_id = $lasted['recharge_order_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        $rechargeOrderNo = $p . str_pad(++$count, 4, '0', STR_PAD_LEFT);
        return $rechargeOrderNo;
    }
}

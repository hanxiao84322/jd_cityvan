<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "approve_log".
 *
 * @property int $id
 * @property int|null $order_type 单据类型 1 订单调整单
 * @property int $order_id 订单id
 * @property int $approve_node 审核节点
 * @property int|null $approve_status 1 通过 2 驳回
 * @property string|null $approve_opinion 审核备注
 * @property string|null $approve_username 审核人用户名
 * @property int|null $approve_name 审核人姓名
 * @property int|null $approve_time 审核时间
 */
class ApproveLog extends \yii\db\ActiveRecord
{
    const ORDER_TYPE_DELIVERY_ADJUST = 1;

    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    public static  $statusList = [
        self::STATUS_APPROVED => '通过',
        self::STATUS_REJECTED => '驳回',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'approve_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_type', 'order_id', 'approve_status'], 'integer'],
            [['order_id'], 'required'],
            [['approve_opinion'], 'string'],
            [['approve_username', 'approve_name', 'approve_time', 'approve_node'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_type' => 'Order Type',
            'order_id' => 'Order ID',
            'approve_node' => '审核节点',
            'approve_status' => '审核状态',
            'approve_opinion' => '审核备注',
            'approve_username' => '审核人用户名',
            'approve_name' => '审核人姓名',
            'approve_time' => '审核时间',
        ];
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }
}

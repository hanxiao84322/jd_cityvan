<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "delivery_adjust_order".
 *
 * @property int $id
 * @property string $logistic_no 订单号
 * @property string|null $adjust_order_no 调整单号
 * @property float|null $adjust_amount 调整金额
 * @property int $type 调整类型 1 客户补偿 2 供应商罚款
 * @property int|null $status 状态 0 新建 1 已完成
 * @property string|null $note 备注
 * @property string|null $first_approve_username 一级审核人用户名
 * @property string|null $first_approve_name 一级审核人姓名
 * @property string|null $first_approve_time 一级审核时间
 * @property string|null $first_approve_opinion 一级审核备注
 * @property string|null $sec_approve_username 二级审核人用户名
 * @property string|null $sec_approve_name 二级审核人姓名
 * @property string|null $sec_approve_time 二级审核时间
 * @property string|null $sec_approve_opinion 二级审核备注
 * @property string|null $create_time 创建时间
 * @property string|null $create_name 创建人用户名
 * @property string|null $update_time 修改时间
 * @property string|null $update_name 修改人用户名
 */
class DeliveryAdjustOrder extends \yii\db\ActiveRecord
{
    public $files = [];


    const TYPE_REWARD = 1;
    const TYPE_FINE = 2;
    const TYPE_CLAIMS_JUDGMENT = 3;

    public static  $typeList = [
        self::TYPE_REWARD => '客户补偿',
        self::TYPE_FINE => '供应商罚款',
        self::TYPE_CLAIMS_JUDGMENT => '京东理赔判责单',
    ];

    const STATUS_CREATE = 0;
    const STATUS_FINISHED = 1;
    const STATUS_FIRST_APPROVED = 2;
    const STATUS_FIRST_REJECTED = 3;
    const STATUS_SEC_APPROVED = 4;
    const STATUS_SEC_REJECTED = 5;

    public static  $statusList = [
        self::STATUS_CREATE => '新建',
        self::STATUS_FIRST_APPROVED => '一级审核通过',
        self::STATUS_FIRST_REJECTED => '一级审核驳回',
        self::STATUS_SEC_APPROVED => '二级审核通过',
        self::STATUS_SEC_REJECTED => '二级审核驳回',
        self::STATUS_FINISHED => '已完成',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_adjust_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_no'], 'required'],
            [['adjust_amount'], 'number'],
            [['type', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['logistic_no', 'first_approve_username', 'first_approve_name', 'first_approve_time', 'sec_approve_username', 'sec_approve_name', 'sec_approve_time'], 'string', 'max' => 50],
            [['adjust_order_no', 'create_name', 'update_name'], 'string', 'max' => 20],
            [['note', 'first_approve_opinion', 'sec_approve_opinion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'logistic_no' => '快递单号',
            'adjust_order_no' => '调整单号',
            'adjust_amount' => '调整金额(元)',
            'type' => '调整类型',
            'status' => '状态',
            'note' => '备注',
            'create_time' => '创建时间',
            'create_name' => '创建人用户名',
            'update_name' => '修改时间',
            'update_time' => '修改人用户名',
        ];
    }


    public static function generateId()
    {
        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        $p = "DAO" . date('ymd');
        if ($lasted && strstr($lasted['adjust_order_no'], $p)) {
            $last_id = $lasted['adjust_order_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        return $p . str_pad(++$count, 4, '0', STR_PAD_LEFT);
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adjust_order".
 *
 * @property int $id
 * @property int|null $customer_id 客户ID
 * @property int|null $institution_id 组织机构ID
 * @property string|null $settlement_no 结算单号
 * @property string|null $adjust_order_no 调整单号
 * @property float|null $adjust_amount 调整金额
 * @property int|null $type 调整类型 1 奖励 2 罚款
 * @property int|null $status 状态 1 正常 2 取消
 * @property string|null $note 备注
 * @property string|null $create_time 创建时间
 * @property string|null $create_name 创建人用户名
 */
class AdjustOrder extends \yii\db\ActiveRecord
{
    public $customer_name;
    const STATUS_CANCEL = 0;
    const STATUS_NORMAL = 1;
    public static $statusList = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_CANCEL => '取消',
    ];

    const TYPE_REWARD = 1;
    const TYPE_FINE = 2;
    public static $typeList = [
        self::TYPE_FINE => '罚款',
        self::TYPE_REWARD => '奖励',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adjust_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['settlement_no'], 'required'],
            [['customer_id', 'institution_id', 'type', 'status'], 'integer'],
            [['adjust_amount'], 'number'],
            [['create_time'], 'safe'],
            [['settlement_no','adjust_order_no'], 'string', 'max' => 50],
            [['note'], 'string', 'max' => 255],
            [['create_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => '客户ID',
            'customer_name' => '客户',
            'institution_id' => '组织机构ID',
            'adjust_order_no' => '调整单号',
            'settlement_no' => '结算单号',
            'adjust_amount' => '调整金额',
            'type' => '调整类型',
            'status' => '状态',
            'note' => '备注',
            'create_time' => '新建时间',
            'create_name' => '新建人用户名',
        ];
    }
    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }
    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function generateId()
    {
        $adjustOrderNo = '';
        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        $p = "SO" . date('ymd');
        if ($lasted && strstr($lasted['adjust_order_no'], $p)) {
            $last_id = $lasted['adjust_order_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        $adjustOrderNo = $p . str_pad(++$count, 4, '0', STR_PAD_LEFT);
        return $adjustOrderNo;
    }
}

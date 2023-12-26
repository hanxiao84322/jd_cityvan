<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_check_bill".
 *
 * @property int $id
 * @property string $logistic_company_check_bill_no 对账单号
 * @property int $logistic_id 快递公司ID
 * @property string $warehouse_code 仓库编码
 * @property int $type 1 付费 2 收费
 * @property string $date 生成日期
 * @property int $logistic_company_order_num 快递公司导入订单数量
 * @property int $system_order_num 系统存在订单数量
 * @property float $logistic_company_order_price 快递公司统计金额
 * @property float $system_order_price 系统统计金额
 * @property string $create_username 创建人用户名
 * @property string $create_time 创建时间
 * @property string $update_username 更新人用户名
 * @property string $update_time 更新时间
 * @property string $note 备注
 * @property int $status 状态 1 待确认 2 已确认 3 已生成结算单
 */
class LogisticCompanyCheckBill extends \yii\db\ActiveRecord
{
    public $logistic_company_name;

    const STATUS_NEW = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_CREATE_SETTLEMENT = 3;
    public static  $statusList = [
        self::STATUS_NEW => '待确认',
        self::STATUS_CONFIRMED => '已确认',
        self::STATUS_CREATE_SETTLEMENT => '已生成结算单',
    ];

    const TYPE_PAY = 1;
    const TYPE_REC = 2;
    public static  $typeList = [
        self::TYPE_PAY => '付费',
        self::TYPE_REC => '收费',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_check_bill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_company_check_bill_no', 'logistic_id', 'warehouse_code', 'date', 'logistic_company_order_num', 'system_order_num', 'logistic_company_order_price', 'system_order_price', 'create_username', 'create_time',  'status'], 'required'],
            [['logistic_id', 'logistic_company_order_num', 'system_order_num', 'status', 'type'], 'integer'],
            [['date', 'create_time', 'update_time'], 'safe'],
            [['logistic_company_order_price', 'system_order_price'], 'number'],
            [['note'], 'string'],
            [['logistic_company_check_bill_no', 'create_username', 'update_username'], 'string', 'max' => 50],
            [['warehouse_code'], 'string', 'max' => 20],
            [['logistic_company_check_bill_no'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistic_company_check_bill_no' => '对账单号',
            'logistic_id' => '快递公司',
            'logistic_company_name' => '快递公司',
            'warehouse_code' => '仓库编码',
            'type' => '类型',
            'date' => '生成日期',
            'logistic_company_order_num' => '导入数据',
            'system_order_num' => '有效数据',
            'logistic_company_order_price' => '导入金额',
            'system_order_price' => '有效金额',
            'create_username' => '创建人用户名',
            'create_time' => '创建时间',
            'update_username' => '更新人用户名',
            'update_time' => '更新时间',
            'note' => '备注',
            'status' => '状态',
        ];
    }

    public static function generateId($type)
    {
        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        if ($type == self::TYPE_PAY) {
            $orderType = 3;
        } else {
            $orderType = 1;
        }
        $p = "ZF" . $orderType. date('ymd');
        if ($lasted && strstr($lasted['logistic_company_check_bill_no'], $p)) {
            $last_id = $lasted['logistic_company_check_bill_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        return $p . str_pad(++$count, 4, '0', STR_PAD_LEFT);
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }
}

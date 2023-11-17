<?php

namespace common\models;

use backend\models\Institution;
use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id 自增ID
 * @property string $name 名称
 * @property int $institution_id 组织机构ID
 * @property int $parent_customer_id 上级客户ID
 * @property int $type 客户类型 1 自营 2 代理
 * @property string $delivery_platform 发货平台 小红书 天猫 抖音
 * @property string $sender_name 发货人姓名
 * @property string $sender_phone 发货人联系电话
 * @property string $sender_company 发货人公司
 * @property string $sender_address 发货地址
 * @property string $order_get_type 快递单获取方式 预分配号段 接口调用
 * @property string $free_type 运费类型 1 首重加续重 2 重量区间
 * @property int $status 客户状态 1 正常 2 禁用
 * @property string $code 客户编码（三方）
 * @property string $create_name 创建人用户名
 * @property string $create_time 创建时间
 * @property string $update_name 更新人用户名
 * @property string $update_time 更新时间
 */
class Customer extends \yii\db\ActiveRecord
{
    public $institution_name;

    const STATUS_CANCEL = 0;
    const STATUS_NORMAL = 1;
    public static $statusList = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_CANCEL => '禁用',
    ];

    const TYPE_SELF = 1;
    const TYPE_AGENT = 2;
    public static $typeList = [
        self::TYPE_SELF => '自营',
        self::TYPE_AGENT => '代理',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'unique', 'targetClass' => '\common\models\Customer', 'message' => '名称已存在.'],
            [['sender_name', 'sender_phone', 'sender_address'], 'requiredIf' => function ($model) {
                return empty($model->sender_name) && empty($model->sender_phone) && empty($model->sender_address);
            }, 'message' => '发货人，发货地址，发货人电话不能同时为空'],
            [['institution_id', 'status', 'type', 'parent_customer_id', 'free_type'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'delivery_platform', 'sender_company', 'order_get_type', 'code', 'create_name', 'update_name'], 'string', 'max' => 50],
            [['sender_name'], 'string', 'max' => 100],
            [['sender_phone'], 'string', 'max' => 50],
            [['sender_address', 'sender_company'], 'string', 'max' => 255],
            [['create_time', 'update_time'], 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'institution_id' => '组织机构',
            'parent_customer_id' => '上级客户ID',
            'type' => '类型',
            'institution_name' => '组织机构',
            'delivery_platform' => '发货平台',
            'sender_name' => '寄件人姓名',
            'sender_phone' => '寄件人联系电话',
            'sender_company' => '寄件人公司',
            'sender_address' => '寄件人地址',
            'order_get_type' => '订单获取方式',
            'status' => '状态',
            'code' => '编码',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '修改人用户名',
            'update_time' => '修改时间',
        ];
    }

    /**
     * 获取状态文字.
     *
     * @param $status
     * @return mixed|string
     */
    public static function getShowStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }
    public static function getFreeTypeName($freeType)
    {
        return isset(self::$freeTypeList[$freeType]) ? self::$freeTypeList[$freeType] : '无';
    }
    public static function getAllByInstitutionId($institutionId, $level)
    {
        if ($level != Institution::LEVEL_PARENT) {
            $res = self::find()->where(['institution_id' => $institutionId, 'status' => self::STATUS_NORMAL])->andWhere('name is not null')->asArray()->all();
        } else {
            $res = self::find()->where(['status' => self::STATUS_NORMAL])->andWhere('name is not null')->asArray()->all();

        }
        return $res;
    }

    public static function getNameById($id)
    {
        $res = self::findOne($id);
        return isset($res->name) ? $res->name : '';
    }

    /**
     * @param $institutionData
     * @param $username
     * @return true|void
     */
    public static function createByInstitution($institutionData, $username)
    {
        $return = [
            'success' => 0,
            'msg' => '',
            'id' => ''
        ];
        $customerExists = Customer::find()->where(['name' => $institutionData['name']])->exists();
        if (!$customerExists) {
            $institution = Institution::findOne(['id' => $institutionData['parent_id']]);
            $customer = Institution::findOne(['id' => $institution->id]);
            $customerModel = new Customer();
            $customerModel->name = $institutionData['name'];
            $customerModel->institution_id = $institution['id'];
            $customerModel->parent_customer_id = $customer['id'];
            $customerModel->type = Customer::TYPE_AGENT;
            $customerModel->create_name = $username;
            $customerModel->create_time = date('Y-m-d H:i:s', time());
            if (!$customerModel->save())
            {
                $return['msg'] = $customerModel->getErrors();
            }
            $return['success'] = 1;
            $return['id'] = $customerModel->attributes['id'];
            return $return;
        }
    }

    public static function getIdByInstitutionId($institutionId)
    {
        $customerId = self::find()->select('c.id')->alias('c')->leftJoin(Institution::tableName() . ' i', 'c.name = i.name')->where(['i.id' => $institutionId])->asArray()->scalar();
        return $customerId;
    }

    public static function getIdByName($name)
    {
        $res = self::findOne(['name' => $name]);
        return isset($res->id) ? $res->id : '';
    }
}
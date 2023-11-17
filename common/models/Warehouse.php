<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $id 自增ID
 * @property string $name 仓库名称
 * @property string $code 仓库编码
 * @property string $contact_name 联系人
 * @property string $contact_phone 联系电话
 * @property string $address 仓库地址
 * @property int $customer_id 客户ID 仓库类型为客户仓时必填
 * @property int $type 仓库类型 01 自有仓 02 客户仓
 * @property int $status 仓库状态 1 可用 2 禁用
 * @property string $collect_area 集货区域
 */
class Warehouse extends \yii\db\ActiveRecord
{

    const STATUS_CANCEL = 0;
    const STATUS_NORMAL = 1;
    public static array $statusList = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_CANCEL => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'status'], 'required'],
            [['customer_id', 'type', 'status'], 'integer'],
            [['name','code','contact_name','contact_phone'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 200],
            [['collect_area'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '仓库名称',
            'code' => '仓库编码',
            'contact_name' => '联系人',
            'contact_phone' => '联系电话',
            'address' => '仓库地址',
            'customer_id' => 'Customer ID',
            'type' => 'Type',
            'status' => '状态',
            'collect_area' => 'Collect Area',
        ];
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getAll()
    {
        return self::find()->asArray()->all();
    }

    public static function getListByJsonId($jsonId)
    {
        $idList = json_decode($jsonId, true);
        $res = self::find()->where(['code' => $idList])->asArray()->all();
        if (!empty($res)) {
            foreach ($res as $value) {
                echo $value['name'] . ",";
            }
        }
    }

}

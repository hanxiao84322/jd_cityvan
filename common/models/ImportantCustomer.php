<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "important_customer".
 *
 * @property int $id
 * @property string $name 姓名
 * @property string $phone 联系电话
 * @property string $address 地址
 * @property string $complaint_type 投诉类型
 * @property int $work_order_num 提出工单数量
 * @property int $level 等级 1 关注 2 重点关注 3主动跟进客户 4 高危客户
 * @property string $create_time 创建时间
 * @property string $create_name 创建人用户名
 * @property string $update_time 更新时间
 * @property string $update_name 更新人
 */
class ImportantCustomer extends \yii\db\ActiveRecord
{
    const LEVEL_ZERO = 0;
    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;
    const LEVEL_FOUR = 4;

    public static $levelList = [
        self::LEVEL_ZERO => '无需关注',
        self::LEVEL_ONE => '关注',
        self::LEVEL_TWO => '重点关注',
        self::LEVEL_THREE => '主动跟进客户',
        self::LEVEL_FOUR => '高危客户',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'important_customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'address', 'complaint_type', 'work_order_num'], 'required'],
            [['work_order_num', 'level'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['create_name', 'update_name'], 'string', 'max' => 20],
            [['name', 'complaint_type', 'phone', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '姓名',
            'phone' => '电话',
            'address' => '地址',
            'complaint_type' => '投诉类型',
            'work_order_num' => '工单数量',
            'level' => '关注等级',
            'create_time' => '新建时间',
            'create_name' => '新建人用户名',
            'update_time' => '更新时间',
            'update_name' => '更新人用户名',
        ];
    }

    public static function getLevelName($level)
    {
        return isset(self::$levelList[$level]) ? self::$levelList[$level] : '无';
    }

    public static function getLevelByNameAndPhone($name, $phone)
    {
        $level = self::find()->select('level')->where(['name' => $name, 'phone' => $phone])->asArray()->scalar();
        if (empty($level)) {
            return 0;
        }
        return $level;
    }

    public static function getLevelByCount($workOrderCount)
    {
        if ($workOrderCount < 2) {
            return 0;
        } elseif (2 < $workOrderCount and $workOrderCount <= 5) {
            return 1;
        } elseif (5 < $workOrderCount and $workOrderCount <= 10) {
            return 2;
        } elseif (10 < $workOrderCount and $workOrderCount <= 20) {
            return 3;
        } elseif (20 < $workOrderCount) {
            return 4;
        }
    }
}

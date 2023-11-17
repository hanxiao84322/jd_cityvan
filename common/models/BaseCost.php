<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "base_cost".
 *
 * @property int $id 自增ID
 * @property string|null $warehouse 集货仓
 * @property string|null $month 月份
 * @property string|null $source 单据号
 * @property float|null $data_service_fee 信息服务费（逐票）
 * @property float|null $month_rent 月租金（冲抵到票）
 * @property int|null $worker_num 人员数量
 * @property float|null $worker_fee 人员费用（冲抵到票）
 * @property float|null $device_fee 设备折旧费用
 * @property string|null $create_name
 * @property string|null $create_time
 * @property string|null $update_name
 * @property string|null $update_time
 */
class BaseCost extends \yii\db\ActiveRecord
{
    const WAREHOUSE_BEIJING = 1;
    const WAREHOUSE_GUANGDONG = 2;
    public static $warehouseList = [
        self::WAREHOUSE_BEIJING => '北京集货仓',
        self::WAREHOUSE_GUANGDONG => '广州集货仓',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'base_cost';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data_service_fee', 'month_rent', 'worker_fee', 'device_fee'], 'number'],
            [['worker_num'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['warehouse', 'source', 'create_name', 'update_name'], 'string', 'max' => 50],
            [['month'], 'string', 'max' => 20],
            [['warehouse', 'month'], 'unique', 'targetAttribute' => ['warehouse', 'month']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse' => '集货仓',
            'month' => '月份',
            'source' => '单据号',
            'data_service_fee' => '信息服务费（逐票）',
            'month_rent' => '月租金（冲抵到票）',
            'worker_num' => '人员数量',
            'worker_fee' => '人员费用（冲抵到票）',
            'device_fee' => '设备折旧费用',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }
    public static function getWarehouseName($warehouse)
    {
        return isset(self::$warehouseList[$warehouse]) ? self::$warehouseList[$warehouse] : '无';
    }

}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "warehouse_logistic_area_delivery_fee".
 *
 * @property int $id 自增ID
 * @property int $warehouse_id 自有仓库ID
 * @property int $logistic_id 快递公司ID
 * @property string $province 目的省
 * @property string $city 目的市
 * @property string $district 目的区县
 * @property float $weight 首重公斤
 * @property float $price 首重价格元
 * @property float $follow_weight 续重公斤
 * @property float $follow_price 续重价格元
 * @property float $return_rate 退货费率% 发货费用
 * @property float $agent_rate 代理费率% 每单
 * @property int $is_cancel 是否作废 0 否 1 是
 * @property string $create_user
 * @property string $create_time
 * @property string $update_user
 * @property string $update_time
 */
class WarehouseLogisticAreaDeliveryFee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse_logistic_area_delivery_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'logistic_id', 'province', 'city', 'district', 'weight', 'price', 'follow_weight', 'follow_price', 'return_rate', 'agent_rate', 'is_cancel', 'create_user', 'create_time', 'update_user', 'update_time'], 'required'],
            [['warehouse_id', 'logistic_id', 'is_cancel'], 'integer'],
            [['weight', 'price', 'follow_weight', 'follow_price', 'return_rate', 'agent_rate'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['province', 'city', 'district', 'create_user', 'update_user'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Warehouse ID',
            'logistic_id' => 'Logistic ID',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'district',
            'weight' => 'Weight',
            'price' => 'Price',
            'follow_weight' => 'Follow Weight',
            'follow_price' => 'Follow Price',
            'return_rate' => 'Return Rate',
            'agent_rate' => 'Agent Rate',
            'is_cancel' => 'Is Cancel',
            'create_user' => 'Create User',
            'create_time' => 'Create Time',
            'update_user' => 'Update User',
            'update_time' => 'Update Time',
        ];
    }
}

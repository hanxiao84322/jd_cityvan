<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_service_daily_efficiency".
 *
 * @property int $id
 * @property string $date 统计日期
 * @property string|null $username 账号
 * @property string|null $name 客服姓名
 * @property string|null $type 账号属性
 * @property int|null $work_order_create_num 工单发起数量
 * @property int|null $work_order_deal_num 工单处理数量
 * @property int|null $work_order_finished_num 工单完成数量
 * @property int|null $work_order_not_finished_num 当日残留未完成工单数
 * @property float|null $work_order_finished_rate 工单解决率
 */
class CustomerServiceDailyEfficiency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_service_daily_efficiency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['date'], 'safe'],
            [['work_order_create_num', 'work_order_deal_num', 'work_order_finished_num', 'work_order_not_finished_num'], 'integer'],
            [['work_order_finished_rate'], 'number'],
            [['username'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => '统计日期',
            'username' => '账号',
            'name' => '客服姓名',
            'type' => '账号属性',
            'work_order_create_num' => '工单发起数量',
            'work_order_deal_num' => '工单处理数量',
            'work_order_finished_num' => '工单完成数量',
            'work_order_not_finished_num' => '当日残留未完成工单数',
            'work_order_finished_rate' => '工单解决率',
        ];
    }
}

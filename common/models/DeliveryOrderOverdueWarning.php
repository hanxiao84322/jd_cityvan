<?php

namespace common\models;

/**
 * This is the model class for table "delivery_order_overdue_warning".
 *
 * @property int $id
 * @property string $date
 * @property string $warehouse_code
 * @property int $logistic_id
 * @property int $less_one_day
 * @property int $one_to_two_day
 * @property int $two_to_three_day
 * @property int $three_to_five_day
 * @property int $five_to_seven_day
 * @property int $more_seven_day
 */
class DeliveryOrderOverdueWarning extends \yii\db\ActiveRecord
{
    public $logistic_company_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_order_overdue_warning';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'warehouse_code', 'logistic_id', 'less_one_day', 'one_to_two_day', 'two_to_three_day', 'three_to_five_day', 'five_to_seven_day', 'more_seven_day'], 'required'],
            [['date'], 'safe'],
            [['logistic_id', 'less_one_day', 'one_to_two_day', 'two_to_three_day', 'three_to_five_day', 'five_to_seven_day', 'more_seven_day'], 'integer'],
            [['warehouse_code'], 'string', 'max' => 20],
            [['date', 'warehouse_code', 'logistic_id'], 'unique', 'targetAttribute' => ['date', 'warehouse_code', 'logistic_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date' => '统计日期',
            'warehouse_code' => '发货仓',
            'logistic_id' => '快递公司',
            'logistic_company_name' => '快递公司',
            'less_one_day' => '超期1天内',
            'one_to_two_day' => '超期1-2天',
            'two_to_three_day' => '超期2-3天',
            'three_to_five_day' => '超期3-5天',
            'five_to_seven_day' => '超期5-7天',
            'more_seven_day' => '7天以上严重超期',
        ];
    }
}

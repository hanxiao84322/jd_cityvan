<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agent".
 *
 * @property int $id 自增ID
 * @property string|null $company_name 供应商名称
 * @property float|null $fee_rate 费率 每笔结算的费率
 */
class Agent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fee_rate'], 'number'],
            [['company_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_name' => 'Company Name',
            'fee_rate' => 'Fee Rate',
        ];
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_settlement_order_adjust_term".
 *
 * @property int $id
 * @property string $settlement_order_no 结算单号
 * @property float $amount 调整金额
 * @property string $content 说明
 */
class LogisticCompanySettlementOrderAdjustTerm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_settlement_order_adjust_term';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['settlement_order_no', 'amount', 'content'], 'required'],
            [['amount'], 'number'],
            [['content'], 'string'],
            [['settlement_order_no'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settlement_order_no' => 'Settlement Order No',
            'amount' => 'Amount',
            'content' => 'Content',
        ];
    }
}

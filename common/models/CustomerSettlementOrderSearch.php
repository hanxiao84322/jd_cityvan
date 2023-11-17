<?php

namespace common\models;

use backend\models\Institution;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerSettlementOrderSearch represents the model behind the search form of `common\models\CustomerSettlementOrder`.
 */
class CustomerSettlementOrderSearch extends CustomerSettlementOrder
{
    public $page_size = 20;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'institution_id', 'customer_id', 'status'], 'integer'],
            [['logistic_no', 'settlement_order_no'], 'string'],
            [[ 'start_time', 'end_time', 'create_name', 'create_time', 'update_name', 'update_time'], 'safe'],
            [['need_receipt_amount', 'need_pay_amount', 'adjust_amount', 'need_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CustomerSettlementOrder::find()->select('cso.*, c.name as customer_name, i.name as institution_name,c.sender_name,c.sender_phone,c.sender_company')->alias('cso')->leftJoin(Customer::tableName() . ' c', 'cso.customer_id = c.id')->leftJoin(Institution::tableName() . ' i', 'cso.institution_id = i.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cso.institution_id' => $this->institution_id,
            'cso.customer_id' => $this->customer_id,
        ]);
        if (!empty($this->logistic_no)) {
            $settlementOrderNo = CustomerSettlementOrderDetail::find()->select('settlement_order_no')->where(['logistic_no' => $this->logistic_no, 'institution_id' => $this->institution_id])->asArray()->scalar();
            if (!empty($settlementOrderNo)) {
                $query->andFilterWhere(['cso.settlement_order_no' => $settlementOrderNo]);
            }
        }
        $query->andFilterWhere(['like', 'cso.settlement_order_no', $this->settlement_order_no]);
//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }
}

<?php

namespace backend\models;

use common\models\Customer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerAreaDeliveryFeeSearch represents the model behind the search form of `backend\models\CustomerAreaDeliveryFee`.
 */
class CustomerAreaDeliveryFeeSearch extends CustomerAreaDeliveryFee
{
    public $page_size = 20;
    public $customer_type;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_customer_id', 'customer_id','institution_id', 'is_cancel', 'customer_type'], 'integer'],
            [['province', 'city', 'district', 'create_user', 'create_time', 'update_user', 'update_time'], 'safe'],
            [['customer_name'], 'string'],
            [['weight', 'price', 'follow_weight', 'invoice_base_price', 'face_order_fee', 'return_fee', 'return_base', 'orders_base_fee', 'under_orders_base_fee', 'follow_price', 'return_rate', 'agent_rate'], 'number'],
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
        $query = CustomerAreaDeliveryFee::find()->select('cadf.*, c.name as customer_name, c.type as customer_type')->alias('cadf')->leftJoin(Customer::tableName() . ' c', 'cadf.customer_id = c.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cadf.city' => $this->city,
            'cadf.institution_id' => $this->institution_id,
            'cadf.province' => $this->province,
            'cadf.district' => $this->district,
            'c.type' => $this->customer_type,
        ]);

        $query->andFilterWhere(['like', 'c.name', $this->customer_name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'create_user', $this->create_user])
            ->andFilterWhere(['like', 'update_user', $this->update_user]);

        return $dataProvider;
    }
}

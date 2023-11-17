<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WeightRangeAreaDeliveryFee;

/**
 * WeightRangeAreaDeliveryFeeSearch represents the model behind the search form of `backend\models\WeightRangeAreaDeliveryFee`.
 */
class WeightRangeAreaDeliveryFeeSearch extends WeightRangeAreaDeliveryFee
{
    public $page_size = 20;
    public $customer_type;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_customer_id', 'customer_id','institution_id', 'is_cancel'], 'integer'],
            [['province', 'city', 'district', 'create_user', 'create_time', 'update_user', 'update_time'], 'safe'],
            [['first_weight_range_price', 'sec_weight_range_price', 'third_weight_range_price', 'fourth_weight_range_price', 'fourth_weight_range_price_float', 'fifth_weight_range_price', 'fifth_weight_range_price_float', 'invoice_base_price', 'face_order_fee', 'return_fee', 'return_base', 'orders_base_fee', 'under_orders_base_fee', 'return_rate', 'agent_rate'], 'number'],
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
        $query = WeightRangeAreaDeliveryFee::find();

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
            'id' => $this->id,
            'parent_customer_id' => $this->parent_customer_id,
            'customer_id' => $this->customer_id,
            'first_weight_range_price' => $this->first_weight_range_price,
            'sec_weight_range_price' => $this->sec_weight_range_price,
            'third_weight_range_price' => $this->third_weight_range_price,
            'fourth_weight_range_price' => $this->fourth_weight_range_price,
            'fourth_weight_range_price_float' => $this->fourth_weight_range_price_float,
            'fifth_weight_range_price' => $this->fifth_weight_range_price,
            'fifth_weight_range_price_float' => $this->fifth_weight_range_price_float,
            'invoice_base_price' => $this->invoice_base_price,
            'face_order_fee' => $this->face_order_fee,
            'return_fee' => $this->return_fee,
            'return_base' => $this->return_base,
            'orders_base_fee' => $this->orders_base_fee,
            'under_orders_base_fee' => $this->under_orders_base_fee,
            'return_rate' => $this->return_rate,
            'agent_rate' => $this->agent_rate,
            'is_cancel' => $this->is_cancel,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'create_user', $this->create_user])
            ->andFilterWhere(['like', 'update_user', $this->update_user]);

        return $dataProvider;
    }
}

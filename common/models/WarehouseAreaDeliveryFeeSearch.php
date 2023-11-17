<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarehouseAreaDeliveryFee;

/**
 * WarehouseAreaDeliveryFeeSearch represents the model behind the search form of `common\models\WarehouseAreaDeliveryFee`.
 */
class WarehouseAreaDeliveryFeeSearch extends WarehouseAreaDeliveryFee
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'warehouse_id', 'is_cancel'], 'integer'],
            [['province', 'city', 'district', 'create_user', 'create_time', 'update_user', 'update_time'], 'safe'],
            [['weight', 'price', 'follow_weight', 'follow_price', 'return_rate', 'agent_rate'], 'number'],
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
        $query = WarehouseAreaDeliveryFee::find();

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
            'warehouse_id' => $this->warehouse_id,
            'weight' => $this->weight,
            'price' => $this->price,
            'follow_weight' => $this->follow_weight,
            'follow_price' => $this->follow_price,
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

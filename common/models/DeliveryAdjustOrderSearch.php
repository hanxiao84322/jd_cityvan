<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DeliveryAdjustOrder;

/**
 * DeliveryAdjustOrderSearch represents the model behind the search form of `common\models\DeliveryAdjustOrder`.
 */
class DeliveryAdjustOrderSearch extends DeliveryAdjustOrder
{
    public int $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status'], 'integer'],
            [['logistic_no', 'adjust_order_no', 'note', 'create_time', 'create_name'], 'safe'],
            [['adjust_amount'], 'number'],
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
        $query = DeliveryAdjustOrder::find();

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
            'adjust_amount' => $this->adjust_amount,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'logistic_no', $this->logistic_no])
            ->andFilterWhere(['like', 'adjust_order_no', $this->adjust_order_no])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'create_name', $this->create_name]);

        return $dataProvider;
    }
}

<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CustomerBalanceLog;

/**
 * CustomerBalanceLogSearch represents the model behind the search form of `common\models\CustomerBalanceLog`.
 */
class CustomerBalanceLogSearch extends CustomerBalanceLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'institution_id', 'customer_id', 'type', 'category'], 'integer'],
            [['before_balance', 'after_balance', 'change_amount'], 'number'],
            [['source', 'change_time'], 'safe'],
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
        $query = CustomerBalanceLog::find();

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
            'institution_id' => $this->institution_id,
            'customer_id' => $this->customer_id,
            'before_balance' => $this->before_balance,
            'after_balance' => $this->after_balance,
            'change_amount' => $this->change_amount,
            'type' => $this->type,
            'category' => $this->category,
            'change_time' => $this->change_time,
        ]);

        $query->andFilterWhere(['like', 'source', $this->source]);

        return $dataProvider;
    }
}

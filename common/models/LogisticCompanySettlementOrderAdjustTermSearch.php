<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticCompanySettlementOrderAdjustTerm;

/**
 * LogisticCompanySettlementOrderAdjustTermSearch represents the model behind the search form of `common\models\LogisticCompanySettlementOrderAdjustTerm`.
 */
class LogisticCompanySettlementOrderAdjustTermSearch extends LogisticCompanySettlementOrderAdjustTerm
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['settlement_order_no', 'content'], 'safe'],
            [['amount'], 'number'],
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
        $query = LogisticCompanySettlementOrderAdjustTerm::find();

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
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'settlement_order_no', $this->settlement_order_no])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}

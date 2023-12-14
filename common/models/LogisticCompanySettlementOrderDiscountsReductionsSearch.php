<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticCompanySettlementOrderDiscountsReductions;

/**
 * LogisticCompanySettlementOrderDiscountsReductionsSearch represents the model behind the search form of `common\models\LogisticCompanySettlementOrderDiscountsReductions`.
 */
class LogisticCompanySettlementOrderDiscountsReductionsSearch extends LogisticCompanySettlementOrderDiscountsReductions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['name', ' content', 'create_username', 'create_time', 'update_username', 'update_time'], 'safe'],
            [['min_price', 'discount', 'sub_price'], 'number'],
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
        $query = LogisticCompanySettlementOrderDiscountsReductions::find();

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
            'type' => $this->type,
            'min_price' => $this->min_price,
            'discount' => $this->discount,
            'sub_price' => $this->sub_price,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', ' content', $this-> content])
            ->andFilterWhere(['like', 'create_username', $this->create_username])
            ->andFilterWhere(['like', 'update_username', $this->update_username]);

        return $dataProvider;
    }
}

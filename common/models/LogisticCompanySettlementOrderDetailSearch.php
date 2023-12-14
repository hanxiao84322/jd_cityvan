<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticCompanySettlementOrderDetail;

/**
 * LogisticCompanySettlementOrderDetailSearch represents the model behind the search form of `common\models\LogisticCompanySettlementOrderDetail`.
 */
class LogisticCompanySettlementOrderDetailSearch extends LogisticCompanySettlementOrderDetail
{
    public int $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id'], 'integer'],
            [['settlement_order_no', 'logistic_no', 'warehouse_code', 'province', 'city', 'district', 'size', 'finish_time', 'create_time'], 'safe'],
            [['weight', 'size_weight', 'need_receipt_amount'], 'number'],
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
        $query = LogisticCompanySettlementOrderDetail::find();

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
            'logistic_id' => $this->logistic_id,
            'weight' => $this->weight,
            'size_weight' => $this->size_weight,
            'need_receipt_amount' => $this->need_receipt_amount,
            'finish_time' => $this->finish_time,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'settlement_order_no', $this->settlement_order_no])
            ->andFilterWhere(['like', 'logistic_no', $this->logistic_no])
            ->andFilterWhere(['like', 'warehouse_code', $this->warehouse_code])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'size', $this->size]);

        return $dataProvider;
    }
}
